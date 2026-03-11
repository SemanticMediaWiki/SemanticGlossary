<?php

namespace SG\Tests\Integration;

use Lingo\Element;
use SG\Cache\ElementsCacheBuilder;
use SG\Cache\GlossaryCache;
use SG\PropertyRegistrationHelper;
use SMW\DIProperty;
use SMW\PropertyRegistry;
use SMW\Tests\SMWIntegrationTestCase;
use SMW\Tests\Utils\UtilityFactory;
use SMWDIBlob as DIBlob;
use HashBagOStuff;

/**
 * @ingroup Test
 *
 * @group SG
 * @group SGExtension
 * @group semantic-mediawiki-integration
 * @group mediawiki-database
 * @group Database
 * @group medium
 *
 * @license GPL-2.0-or-later
 * @since 1.1
 *
 * @author mwjames
 * @author Youri van den Bogert
 */
class MwDBSQLStoreIntegrationTest extends SMWIntegrationTestCase {

	/**
	 * @var array
	 */
	private $subjectsToBeCleared = [];

	/**
	 * @var \SMW\Tests\Utils\Fixtures\SemanticDataFactory
	 */
	private $semanticDataFactory;

	protected function setUp(): void {
		parent::setUp();

		$this->semanticDataFactory = UtilityFactory::getInstance()->newSemanticDataFactory();

		// Ensure SemanticGlossary properties are registered
		$propertyRegistrationHelper = new PropertyRegistrationHelper( PropertyRegistry::getInstance() );
		$propertyRegistrationHelper->registerProperties();
	}

	protected function tearDown(): void {
		foreach ( $this->subjectsToBeCleared as $subject ) {
			$this->getStore()->deleteSubject( $subject->getTitle() );
		}

		parent::tearDown();
	}

	/**
	 * @covers \SG\PropertyRegistrationHelper
	 */
	public function testPageCreateDeleteStoreIntegration() {
		$store = $this->getStore();

		$semanticData = $this->semanticDataFactory
			->setTitle( __METHOD__ )
			->newEmptySemanticData();

		$this->subjectsToBeCleared[] = $semanticData->getSubject();

		$semanticData->addPropertyObjectValue(
			new DIProperty( PropertyRegistrationHelper::SG_TERM ),
			new DIBlob( 'testTerm' )
		);
		$semanticData->addPropertyObjectValue(
			new DIProperty( PropertyRegistrationHelper::SG_DEFINITION ),
			new DIBlob( 'testDefinition' )
		);

		$store->updateData( $semanticData );

		$values = $store->getPropertyValues(
			$semanticData->getSubject(),
			new DIProperty( PropertyRegistrationHelper::SG_TERM )
		);

		$this->assertNotEmpty( $values );
		$this->assertSame( 'testTerm', $values[0]->getString() );

		// Clear semantic data for the subject
		$store->clearData( $semanticData->getSubject() );

		$values = $store->getPropertyValues(
			$semanticData->getSubject(),
			new DIProperty( PropertyRegistrationHelper::SG_TERM )
		);

		$this->assertSame( [], $values );
	}

	/**
	 * @covers \SG\Maintenance\RebuildGlossaryCache
	 */
	public function testRebuildGlossaryCacheMaintenanceRun() {
		$runnerFactory = UtilityFactory::getInstance()->newRunnerFactory();

		$maintenanceRunner = $runnerFactory->newMaintenanceRunner(
			'SG\Maintenance\RebuildGlossaryCache'
		);

		$this->assertTrue(
			$maintenanceRunner->setQuiet()->run()
		);
	}

	/**
	 * @covers \SG\Cache\ElementsCacheBuilder::getElements
	 * @covers \SG\Cache\ElementsCacheBuilder::buildQuery
	 *
	 * Tests that glossary terms with spaces can be found when searching for
	 * a partial match. For example, searching for "Workflow" should find
	 * a glossary term "Workflow Schema".
	 *
	 * @see https://github.com/SemanticMediaWiki/SemanticGlossary/issues/85
	 */
	public function testGetElementsFindsTermsWithSpaces() {
		$store = $this->getStore();

		$semanticData = $this->semanticDataFactory
			->setTitle( __METHOD__ )
			->newEmptySemanticData();

		$this->subjectsToBeCleared[] = $semanticData->getSubject();

		// Create a glossary entry with a term containing spaces
		$semanticData->addPropertyObjectValue(
			new DIProperty( PropertyRegistrationHelper::SG_TERM ),
			new DIBlob( 'Workflow Schema' )
		);
		$semanticData->addPropertyObjectValue(
			new DIProperty( PropertyRegistrationHelper::SG_DEFINITION ),
			new DIBlob( 'A schema that defines workflow processes' )
		);
		$semanticData->addPropertyObjectValue(
			new DIProperty( PropertyRegistrationHelper::SG_LINK ),
			new DIBlob( 'Workflow_Schema' )
		);
		$semanticData->addPropertyObjectValue(
			new DIProperty( PropertyRegistrationHelper::SG_STYLE ),
			new DIBlob( 'glossary-term' )
		);

		$store->updateData( $semanticData );

		// Verify data was stored correctly
		$storedTerms = $store->getPropertyValues(
			$semanticData->getSubject(),
			new DIProperty( PropertyRegistrationHelper::SG_TERM )
		);
		$this->assertNotEmpty( $storedTerms, 'Term should be stored' );
		$this->assertSame( 'Workflow Schema', $storedTerms[0]->getString() );

		// Now test that ElementsCacheBuilder can find this term
		// when searching for a partial match (just "Workflow")
		$glossaryCache = new GlossaryCache( new HashBagOStuff() );
		$elementsCacheBuilder = new ElementsCacheBuilder( $store, $glossaryCache );

		// Search for partial term - should find "Workflow Schema"
		$results = $elementsCacheBuilder->getElements( [ 'Workflow' ] );

		$this->assertNotEmpty(
			$results,
			'Should find glossary term "Workflow Schema" when searching for "Workflow"'
		);

		$this->assertSame(
			'Workflow Schema',
			$results[0][Element::ELEMENT_TERM],
			'The returned term should be "Workflow Schema"'
		);

		$this->assertSame(
			'A schema that defines workflow processes',
			$results[0][Element::ELEMENT_DEFINITION],
			'The returned definition should match'
		);
	}

	/**
	 * @covers \SG\Cache\ElementsCacheBuilder::getElements
	 * @covers \SG\Cache\ElementsCacheBuilder::getTerms
	 * @covers \SG\Cache\ElementsCacheBuilder::buildElements
	 *
	 * Tests that a glossary page with multiple terms returns all terms
	 * when any of them is searched for.
	 *
	 * @see https://github.com/SemanticMediaWiki/SemanticGlossary/issues/85
	 */
	public function testGetElementsFindsMultipleTermsOnSamePage() {
		$store = $this->getStore();

		$semanticData = $this->semanticDataFactory
			->setTitle( __METHOD__ )
			->newEmptySemanticData();

		$this->subjectsToBeCleared[] = $semanticData->getSubject();

		// Create a glossary entry with multiple terms (synonyms)
		$semanticData->addPropertyObjectValue(
			new DIProperty( PropertyRegistrationHelper::SG_TERM ),
			new DIBlob( 'Workflow' )
		);
		$semanticData->addPropertyObjectValue(
			new DIProperty( PropertyRegistrationHelper::SG_TERM ),
			new DIBlob( 'Workflows' )
		);
		$semanticData->addPropertyObjectValue(
			new DIProperty( PropertyRegistrationHelper::SG_DEFINITION ),
			new DIBlob( 'A sequence of processes' )
		);
		$semanticData->addPropertyObjectValue(
			new DIProperty( PropertyRegistrationHelper::SG_LINK ),
			new DIBlob( 'Workflow' )
		);
		$semanticData->addPropertyObjectValue(
			new DIProperty( PropertyRegistrationHelper::SG_STYLE ),
			new DIBlob( 'glossary-term' )
		);

		$store->updateData( $semanticData );

		// Verify data was stored correctly
		$storedTerms = $store->getPropertyValues(
			$semanticData->getSubject(),
			new DIProperty( PropertyRegistrationHelper::SG_TERM )
		);
		$this->assertCount( 2, $storedTerms, 'Both terms should be stored' );

		// Test that ElementsCacheBuilder returns both terms when searching for one
		$glossaryCache = new GlossaryCache( new HashBagOStuff() );
		$elementsCacheBuilder = new ElementsCacheBuilder( $store, $glossaryCache );

		$results = $elementsCacheBuilder->getElements( [ 'Workflow' ] );

		$this->assertCount(
			2,
			$results,
			'Should return both terms (Workflow and Workflows) when searching for "Workflow"'
		);

		$terms = array_map( static fn ( $r ) => $r[Element::ELEMENT_TERM], $results );
		sort( $terms );

		$this->assertSame(
			[ 'Workflow', 'Workflows' ],
			$terms,
			'Both terms should be returned'
		);

		// Verify both have the same definition
		foreach ( $results as $result ) {
			$this->assertSame(
				'A sequence of processes',
				$result[Element::ELEMENT_DEFINITION],
				'Both terms should share the same definition'
			);
		}
	}

}
