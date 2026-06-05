<?php

namespace SG\Tests\Cache;

use SG\Cache\GlossaryCache;
use SMW\DataItems\WikiPage;
use Wikimedia\ObjectCache\BagOStuff;

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
			BagOStuff::class,
			$instance->getCache()
		);
	}

	public function testGetCacheType() {
		$instance = new GlossaryCache();

		$this->assertIsInt(
			$instance->getCacheType()
		);
	}

	public function testGetCacheTypeRespectsLingoCacheType() {
		$instance = new GlossaryCache();

		$previousLingo = $GLOBALS['wgexLingoCacheType'] ?? null;
		$previousMain = $GLOBALS['wgMainCacheType'];

		// Pick a Lingo cache type distinct from the main cache type so the
		// assertion fails if the (formerly mistyped) override is ignored and
		// the main cache type is returned instead.
		$GLOBALS['wgexLingoCacheType'] = CACHE_DB;
		$GLOBALS['wgMainCacheType'] = CACHE_NONE;

		try {
			$this->assertSame(
				CACHE_DB,
				$instance->getCacheType()
			);
		} finally {
			$GLOBALS['wgexLingoCacheType'] = $previousLingo;
			$GLOBALS['wgMainCacheType'] = $previousMain;
		}
	}

	public function testGetKeys() {
		$instance = new GlossaryCache();

		$subject = $this->getMockBuilder( WikiPage::class )
			->disableOriginalConstructor()
			->getMock();

		$subject->expects( $this->once() )
			->method( 'getSerialization' )
			->willReturn( 'Foo' );

		$this->assertIsString(
			$instance->getKeyForSubject( $subject )
		);

		$this->assertIsString(
			$instance->getKeyForLingo()
		);
	}

}
