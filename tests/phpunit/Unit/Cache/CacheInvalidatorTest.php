<?php

namespace SG\Tests\Cache;

use SG\PropertyRegistrationHelper;
use SG\Cache\CacheInvalidator;
use SG\Cache\GlossaryCache;
use SMW\Subobject;
use SMW\SemanticData;
use SMW\DIWikiPage;
use SMW\DIProperty;
use SMWDIBlob as DIBlob;
use HashBagOStuff;
use Title;

/**
 * @covers \SG\Cache\CacheInvalidator
 *
 * @ingroup Test
 *
 * @group SG
 * @group SGExtension
 * @group extension-semantic-glossary
 *
 * @license GPL-2.0-or-later
 * @since 1.0
 *
 * @author mwjames
 */
class CacheInvalidatorTest extends \PHPUnit\Framework\TestCase {

	public function testCanConstruct() {
		CacheInvalidator::clear();

		$this->assertInstanceOf(
			'\SG\Cache\CacheInvalidator',
			CacheInvalidator::getInstance()
		);
	}

	public function testInvalidateOnUpdateWithEmptyData() {
		$store = $this->getMockBuilder( '\SMW\Store' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$store->method( 'getPropertyValues' )
			->willReturn( [] );

		$semanticData = $this->getMockBuilder( '\SMW\SemanticData' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$instance = new CacheInvalidator();
		$instance->setCache( new GlossaryCache( new HashBagOStuff() ) );

		$this->assertTrue( $instance->invalidateCacheOnStoreUpdate( $store, $semanticData ) );
	}

	public function testInvalidateOnUpdateWithNullSemanticData() {
		$store = $this->getMockBuilder( '\SMW\Store' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$instance = new CacheInvalidator();
		$instance->setCache( new GlossaryCache( new HashBagOStuff() ) );

		$this->assertFalse(
			$instance->invalidateCacheOnStoreUpdate( $store, null )
		);
	}

	public function testInvalidateOnUpdateWithDifferentSubobjectData() {
		$subject = DIWikiPage::newFromTitle( Title::newFromText( __METHOD__ ) );

		$subobject = new Subobject( $subject->getTitle() );
		$subobject->setSemanticData( '_999999' );

		$subobject->getSemanticData()->addPropertyObjectValue(
			new DIProperty( PropertyRegistrationHelper::SG_TERM ),
			new DIBlob( 'Foo' )
		);

		$subobject->getSemanticData()->addPropertyObjectValue(
			new DIProperty( PropertyRegistrationHelper::SG_DEFINITION ),
			new DIBlob( 'Bar' )
		);

		$store = $this->getMockBuilder( 'SMWStore' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$store->method( 'getPropertyValues' )
			->willReturn( [] );

		$semanticData = new SemanticData( $subject );
		$semanticData->addPropertyObjectValue(
			$subobject->getProperty(),
			$subobject->getContainer()
		);

		$glossaryCache = new GlossaryCache( new HashBagOStuff() );

		$itemId = $glossaryCache->getKeyForSubject(
			$subobject->getSemanticData()->getSubject()
		);

		$glossaryCache->getCache()->set( $itemId, 'preset.cacheitem' );

		$instance = new CacheInvalidator();
		$instance->setCache( $glossaryCache );

		$this->assertTrue( $instance->invalidateCacheOnStoreUpdate( $store, $semanticData ) );

		$this->assertFalse(
			$glossaryCache->getCache()->get( $itemId ),
			'Asserts that the preset item has been removed from cache'
		);
	}

	public function testInvalidateOnDeleteWithEmptyData() {
		$subject = DIWikiPage::newFromTitle( Title::newFromText( __METHOD__ ) );

		$store = $this->getMockBuilder( 'SMWStore' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$store->expects( $this->once() )
			->method( 'getProperties' )
			->willReturn( array() );

		$instance = new CacheInvalidator();
		$instance->setCache( new GlossaryCache( new HashBagOStuff() ) );

		$this->assertTrue( $instance->invalidateCacheOnPageDelete( $store, $subject ) );
	}

	public function testInvalidateOnDeleteWithSubobject() {
		$subobject  = new DIProperty( '_SOBJ' );
		$subject    = DIWikiPage::newFromTitle( Title::newFromText( __METHOD__ ) );
		$newSubject = DIWikiPage::newFromTitle( Title::newFromText( 'Subobject' ) );

		$store = $this->getMockBuilder( 'SMWStore' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$store->expects( $this->once() )
			->method( 'getProperties' )
			->willReturn( array( '_SOBJ' => $subobject ) );

		$store->expects( $this->once() )
			->method( 'getPropertyValues' )
			->willReturn( $newSubject );

		$glossaryCache = new GlossaryCache( new HashBagOStuff() );

		$itemId = $glossaryCache->getKeyForSubject( $subject );

		$glossaryCache->getCache()->set( $itemId, 'preset.cacheitem' );

		$instance = new CacheInvalidator();
		$instance->setCache( $glossaryCache );

		$this->assertTrue( $instance->invalidateCacheOnPageDelete( $store, $subject ) );

		$this->assertFalse(
			$glossaryCache->getCache()->get( $itemId ),
			'Asserts that the preset item has been removed from cache'
		);
	}

	public function testInvalidateOnMove() {
		$title = Title::newFromText( __METHOD__ );

		$instance = new CacheInvalidator();
		$instance->setCache( new GlossaryCache( new HashBagOStuff() ) );

		$this->assertTrue( $instance->invalidateCacheOnPageMove( $title ) );
	}

}
