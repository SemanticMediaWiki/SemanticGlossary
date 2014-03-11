<?php

namespace SG\Tests;

use SG\PropertyRegistry;
use SG\CacheInvalidator;
use SG\CacheHelper;

use SMW\Subobject;
use SMW\SemanticData;
use SMW\DIWikiPage;
use SMW\DIProperty;
use SMWDIBlob as DIBlob;

use HashBagOStuff;
use Title;

/**
 * @covers \SG\CacheInvalidator
 *
 * @ingroup Test
 *
 * @group SG
 * @group SGExtension
 *
 * @licence GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class CacheInvalidatorTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {
		CacheInvalidator::clear();
		$this->assertInstanceOf( '\SG\CacheInvalidator', CacheInvalidator::getInstance() );
	}

	public function testInvalidateOnUpdateWithEmptyData() {

		$store = $this->getMockBuilder( 'SMWStore' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$semanticData = $this->getMockBuilder( 'SMWSemanticData' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$instance = new CacheInvalidator;
		$instance->setCache( new HashBagOStuff );

		$this->assertTrue( $instance->invalidateCacheOnStoreUpdate( $store, $semanticData ) );
	}

	public function testInvalidateOnUpdateWithDifferentSubobjectData() {

		$subject = DIWikiPage::newFromTitle( Title::newFromText( __METHOD__ ) );

		$subobject = new Subobject( $subject->getTitle() );
		$subobject->setSemanticData( '_999999' );

		$subobject->getSemanticData()->addPropertyObjectValue(
			new DIProperty( PropertyRegistry::SG_TERM ),
			new DIBlob( 'Foo' )
		);

		$subobject->getSemanticData()->addPropertyObjectValue(
			new DIProperty( PropertyRegistry::SG_DEFINITION ),
			new DIBlob( 'Bar' )
		);

		$store = $this->getMockBuilder( 'SMWStore' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$semanticData = new SemanticData( $subject );
		$semanticData->addPropertyObjectValue(
			$subobject->getProperty(),
			$subobject->getContainer()
		);

		$itemId = CacheHelper::getKey( $subobject->getSemanticData()->getSubject() );

		$cache = new HashBagOStuff;
		$cache->set( $itemId, 'preset.cacheitem' );

		$instance = new CacheInvalidator;
		$instance->setCache( $cache );

		$this->assertTrue( $instance->invalidateCacheOnStoreUpdate( $store, $semanticData ) );

		$this->assertFalse(
			$cache->get( $itemId ),
			'Asserts that the preset item has been removed from cache'
		);
	}

	public function testInvalidateOnDeleteWithEmptyData() {

		$subject = \SMWDIWikiPage::newFromTitle( \Title::newFromText( __METHOD__ ) );

		$store = $this->getMockBuilder( 'SMWStore' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$store->expects( $this->once() )
			->method( 'getProperties' )
			->with( $this->equalTo( $subject ) )
			->will( $this->returnValue( array() ) );

		$instance = new CacheInvalidator;
		$instance->setCache( new HashBagOStuff );

		$this->assertTrue( $instance->invalidateCacheOnPageDelete( $store, $subject ) );
	}

	public function testInvalidateOnDeleteWithSubobject() {

		$subobject  = new \SMWDIProperty( '_SOBJ' );
		$subject    = \SMWDIWikiPage::newFromTitle( \Title::newFromText( __METHOD__ ) );
		$newSubject = \SMWDIWikiPage::newFromTitle( \Title::newFromText( 'Subobject' ) );

		$store = $this->getMockBuilder( 'SMWStore' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$store->expects( $this->once() )
			->method( 'getProperties' )
			->with( $this->equalTo( $subject ) )
			->will( $this->returnValue( array( '_SOBJ' => $subobject ) ) );

		$store->expects( $this->once() )
			->method( 'getPropertyValues' )
			->with(
				$this->equalTo( $subject ),
				$this->equalTo( $subobject ) )
			->will( $this->returnValue( $newSubject ) );

		$itemId = CacheHelper::getKey( $subject );

		$cache = new HashBagOStuff;
		$cache->set( $itemId, 'preset.cacheitem' );

		$instance = new CacheInvalidator;
		$instance->setCache( $cache );

		$this->assertTrue( $instance->invalidateCacheOnPageDelete( $store, $subject ) );

		$this->assertFalse(
			$cache->get( $itemId ),
			'Asserts that the preset item has been removed from cache'
		);
	}

	public function testInvalidateOnMove() {

		$title = \Title::newFromText( __METHOD__ );

		$instance = new CacheInvalidator;
		$instance->setCache( new HashBagOStuff );

		$this->assertTrue( $instance->invalidateCacheOnPageMove( $title ) );
	}

}
