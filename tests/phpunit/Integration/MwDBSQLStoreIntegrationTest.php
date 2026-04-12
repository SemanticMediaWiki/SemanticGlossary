<?php

namespace SG\Tests\Integration;

use MediaWikiIntegrationTestCase;
use MediaWiki\Title\Title;
use SG\PropertyRegistrationHelper;
use SMW\DIProperty;
use SMW\DIWikiPage;
use SMW\PropertyRegistry;
use SMW\SemanticData;
use SMW\StoreFactory;
use SMW\Tests\Utils\UtilityFactory;
use SMWDIBlob as DIBlob;

/**
 * @ingroup Test
 *
 * @group SG
 * @group SGExtension
 * @group Database
 * @group medium
 *
 * @license GPL-2.0-or-later
 * @since 1.1
 *
 * @author mwjames
 */
class MwDBSQLStoreIntegrationTest extends MediaWikiIntegrationTestCase {

	protected function setUp(): void {
		parent::setUp();

		// Disable deferred updates so SMW writes semantic data synchronously
		$this->setMwGlobals( 'smwgEnabledDeferredUpdate', false );

		// Reset SMW store to use the test database connection
		StoreFactory::clear();

		// Ensure SemanticGlossary properties are registered
		$propertyRegistrationHelper = new PropertyRegistrationHelper( PropertyRegistry::getInstance() );
		$propertyRegistrationHelper->registerProperties();
	}

	public function testPageCreateDeleteStoreIntegration() {
		$store = StoreFactory::getStore();

		$title = Title::newFromText( 'TestGlossaryPage' );

		$this->editPage( $title, 'Glossary test page' );

		$subject = DIWikiPage::newFromTitle( $title );

		// Directly store semantic data (SMW's parsing hooks do not fire in the test context)
		$semanticData = new SemanticData( $subject );
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
			$subject,
			new DIProperty( PropertyRegistrationHelper::SG_TERM )
		);

		$this->assertNotEmpty( $values );
		$this->assertSame( 'testTerm', $values[0]->getString() );

		// Clear semantic data for the subject
		$store->clearData( $subject );

		$values = $store->getPropertyValues(
			$subject,
			new DIProperty( PropertyRegistrationHelper::SG_TERM )
		);

		$this->assertSame( [], $values );
	}

	public function testRebuildGlossaryCacheMaintenanceRun() {
		$runnerFactory = UtilityFactory::getInstance()->newRunnerFactory();

		$maintenanceRunner = $runnerFactory->newMaintenanceRunner(
			'SG\Maintenance\RebuildGlossaryCache'
		);

		$this->assertTrue(
			$maintenanceRunner->setQuiet()->run()
		);
	}

}
