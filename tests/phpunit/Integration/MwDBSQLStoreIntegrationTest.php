<?php

namespace SG\Tests\Integration;

use SG\PropertyRegistrationHelper;
use SMW\Tests\Utils\UtilityFactory;
use SMW\DIProperty;
use SMW\DIWikiPage;
use Title;

/**
 * @ingroup Test
 *
 * @group SG
 * @group SGExtension
 * @group mediawiki-database
 * @group medium
 *
 * @license GPL-2.0-or-later
 * @since 1.1
 *
 * @author mwjames
 */
class MwDBSQLStoreIntegrationTest extends \PHPUnit\Framework\TestCase {

	/** @var PageCreator */
	private $pageCreator;
	/** @var PageDeleter */
	private $pageDeleter;
	/** @var RunnerFactory */
	private $runnerFactory;

	protected function setUp(): void {
		parent::setUp();

		$this->pageCreator = UtilityFactory::getInstance()->newPageCreator();
		$this->pageDeleter = UtilityFactory::getInstance()->newPageDeleter();
		$this->runnerFactory = UtilityFactory::getInstance()->newRunnerFactory();
	}

	public function testPageCreateDeleteStoreIntegration() {
		$this->markTestSkipped(
			'This test should be revised in the next release'
		);

		if ( !$this->isUsableUnitTestDatabase() ) {
			$this->markTestSkipped(
				'The database setup did not meet the test requirements'
			);
		}

		$title = Title::newFromText( __METHOD__ );

		$this->pageCreator
			->createPage( $title )
			->doEdit( "[[Glossary-Term::testTerm]] [[Glossary-Definition::testDefinition]]" );

		$values = $this->getStore()->getPropertyValues(
			DIWikiPage::newFromTitle( $title ),
			new DIProperty( PropertyRegistrationHelper::SG_TERM )
		);

		$this->assertNotEmpty( $values );

		$this->pageDeleter
			->deletePage( $title );

		$values = $this->getStore()->getPropertyValues(
			DIWikiPage::newFromTitle( $title ),
			new DIProperty( PropertyRegistrationHelper::SG_TERM )
		);

		$this->assertCount( 0, $values );
	}

	public function testRebuildGlossaryCacheMaintenanceRun() {
		$this->markTestSkipped(
			'This test should be revised in the next release'
		);

		if ( !$this->isUsableUnitTestDatabase() ) {
			$this->markTestSkipped(
				'The database setup did not meet the test requirements'
			);
		}

		$this->pageCreator
			->createPage( Title::newFromText( __METHOD__ ) )
			->doEdit( "[[Glossary-Term::testTerm]] [[Glossary-Definition::testDefinition]]" );

		$maintenanceRunner = $this->runnerFactory->newMaintenanceRunner( 'SG\Maintenance\RebuildGlossaryCache' );

		$this->assertTrue(
			$maintenanceRunner->setQuiet()->run()
		);
	}

}
