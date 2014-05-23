<?php

namespace SG\Tests\Cache;

use SG\Cache\GlossaryCache;

/**
 * @covers \SG\Cache\GlossaryCache
 *
 * @ingroup Test
 *
 * @group SG
 * @group SGExtension
 * @group extension-semantic-glossary
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class GlossaryCacheTest extends \PHPUnit_Framework_TestCase {

	public function testGetDefaultCache() {

		$glossaryCache = new GlossaryCache();

		$this->assertInstanceOf(
			'BagOStuff',
			$glossaryCache->getCache()
		);
	}

	public function testGetCacheType() {
		$this->assertInternalType( 'integer', GlossaryCache::getCacheType() );
	}

	public function testGetKeys() {

		$subject = $this->getMockBuilder( '\SMW\DIWikiPage' )
			->disableOriginalConstructor()
			->getMock();

		$subject->expects( $this->once() )
			->method( 'getSerialization' )
			->will( $this->returnValue( 'Foo' ) );

		$this->assertInternalType(
			'string',
			GlossaryCache::getKeyForSubject( $subject )
		);

		$this->assertInternalType(
			'string',
			GlossaryCache::getKeyForLingo()
		);
	}

}
