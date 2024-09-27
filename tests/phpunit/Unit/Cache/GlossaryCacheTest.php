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
 * @license GPL-2.0-or-later
 * @since 1.0
 *
 * @author mwjames
 */
class GlossaryCacheTest extends \PHPUnit\Framework\TestCase {

	public function testGetDefaultCache() {
		$instance = new GlossaryCache();

		$this->assertInstanceOf(
			'BagOStuff',
			$instance->getCache()
		);
	}

	public function testGetCacheType() {
		$instance = new GlossaryCache();

		$this->assertIsInt(
			$instance->getCacheType()
		);
	}

	public function testGetKeys() {
		$instance = new GlossaryCache();

		$subject = $this->getMockBuilder( '\SMW\DIWikiPage' )
			->disableOriginalConstructor()
			->getMock();

		$subject->expects( $this->once() )
			->method( 'getSerialization' )
			->will( $this->returnValue( 'Foo' ) );

		$this->assertIsString(
			$instance->getKeyForSubject( $subject )
		);

		$this->assertIsString(
			$instance->getKeyForLingo()
		);
	}

}
