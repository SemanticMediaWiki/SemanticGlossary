<?php

namespace SG\Tests;

use SG\PropertyRegistry;

use SMW\Tests\MwDBaseUnitTestCase;
use SMW\Tests\Util\PageCreator;
use SMW\Tests\Util\PageDeleter;
use SMW\Tests\Util\MaintenanceRunner;

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
 * @license GNU GPL v2+
 * @since 1.1
 *
 * @author mwjames
 */
class MwDBSQLStoreIntegrationTest extends MwDBaseUnitTestCase {

	public function testPageCreateDeleteStoreIntegration() {

		if ( !$this->isUsableUnitTestDatabase() ) {
			$this->markTestSkipped(
				'The database setup did not meet the test requirements'
			);
		}

		$title = Title::newFromText( __METHOD__ );

		$pageCreator = new PageCreator();
		$pageCreator
			->createPage( $title )
			->doEdit( "[[Glossary-Term::testTerm]] [[Glossary-Definition::testDefinition]]" );

		$values = $this->getStore()->getPropertyValues(
			DIWikiPage::newFromTitle( $title ),
			new DIProperty( PropertyRegistry::SG_TERM )
		);

		$this->assertNotEmpty( $values );

		$pageDeleter = new PageDeleter();
		$pageDeleter
			->deletePage( $title );

		$values = $this->getStore()->getPropertyValues(
			DIWikiPage::newFromTitle( $title ),
			new DIProperty( PropertyRegistry::SG_TERM )
		);

		$this->assertEmpty( $values );
	}

	public function testRebuildGlossaryCacheMaintenanceRun() {

		if ( !$this->isUsableUnitTestDatabase() ) {
			$this->markTestSkipped(
				'The database setup did not meet the test requirements'
			);
		}

		$pageCreator = new PageCreator();
		$pageCreator
			->createPage( Title::newFromText( __METHOD__ ) )
			->doEdit( "[[Glossary-Term::testTerm]] [[Glossary-Definition::testDefinition]]" );

		$maintenanceRunner = new MaintenanceRunner( 'SG\Maintenance\RebuildGlossaryCache' );

		$this->assertTrue(
			$maintenanceRunner->setQuiet()->run()
		);
	}

}
