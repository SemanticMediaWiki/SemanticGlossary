<?php

namespace SG\Tests;

use SG\CacheHelper;

/**
 * @covers \SG\CacheHelper
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
class CacheHelperTest extends \PHPUnit_Framework_TestCase {

	public function testGetCache() {
		$this->assertInstanceOf( 'BagOStuff', CacheHelper::getCache() );
	}

	public function testGetCacheType() {
		$this->assertInternalType( 'integer', CacheHelper::getCacheType() );
	}

	public function testGetKey() {

		$subject = $this->getMockBuilder( 'SMWDIWikiPage' )
			->disableOriginalConstructor()
			->getMock();

		$subject->expects( $this->once() )
			->method( 'getSerialization' )
			->will( $this->returnValue( 'Foo' ) );

		$this->assertInternalType( 'string', CacheHelper::getKey( $subject ) );
	}

}
