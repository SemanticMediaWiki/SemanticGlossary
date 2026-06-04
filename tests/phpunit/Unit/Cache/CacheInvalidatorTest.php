<?php

namespace SG\Tests\Cache;

use HashBagOStuff;
use MediaWiki\Title\Title;
use SG\Cache\CacheInvalidator;
use SG\Cache\GlossaryCache;
use SG\PropertyRegistrationHelper;
use SMW\DataItems\Blob;
use SMW\DataItems\Property;
use SMW\DataItems\WikiPage;
use SMW\DataModel\SemanticData;
use SMW\DataModel\Subobject;

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

		$subject = WikiPage::newFromTitle( Title::newFromText( __METHOD__ ) );
		$semanticData = new SemanticData( $subject );

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
		$subject = WikiPage::newFromTitle( Title::newFromText( __METHOD__ ) );

		$subobject = new Subobject( $subject->getTitle() );
		$subobject->setEmptyContainerForId( '_999999' );

		$subobject->getSemanticData()->addPropertyObjectValue(
			new Property( PropertyRegistrationHelper::SG_TERM ),
			new Blob( 'Foo' )
		);

		$subobject->getSemanticData()->addPropertyObjectValue(
			new Property( PropertyRegistrationHelper::SG_DEFINITION ),
			new Blob( 'Bar' )
		);

		$store = $this->getMockBuilder( '\SMW\Store' )
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
		$subject = WikiPage::newFromTitle( Title::newFromText( __METHOD__ ) );

		$store = $this->getMockBuilder( '\SMW\Store' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$store->expects( $this->once() )
			->method( 'getProperties' )
			->with( $subject )
			->willReturn( [] );

		$instance = new CacheInvalidator();
		$instance->setCache( new GlossaryCache( new HashBagOStuff() ) );

		$this->assertTrue( $instance->invalidateCacheOnPageDelete( $store, $subject ) );
	}

	public function testInvalidateOnDeleteWithSubobject() {
		$subobject  = new Property( '_SOBJ' );
		$subject    = WikiPage::newFromTitle( Title::newFromText( __METHOD__ ) );
		$newSubject = WikiPage::newFromTitle( Title::newFromText( 'Subobject' ) );

		$store = $this->getMockBuilder( '\SMW\Store' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$store->expects( $this->once() )
			->method( 'getProperties' )
			->with( $subject )
			->willReturn( [ '_SOBJ' => $subobject ] );

		$store->expects( $this->once() )
			->method( 'getPropertyValues' )
			->with( $subject, $subobject )
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
