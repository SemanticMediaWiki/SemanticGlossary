<?php

namespace SG\Tests;

use SG\Cache\GlossaryCache;

/**
 * @ingroup Test
 *
 * @group SG
 * @group SGExtension
 *
 * @license GPL-2.0-or-later
 * @since 1.0
 *
 * @author mwjames
 */
class ConfigurationIntegrityTest extends \PHPUnit\Framework\TestCase {

	public function testValidityOfCacheTypeSetting() {
		$instance = new GlossaryCache();

		if ( isset( $GLOBAL['wgexLingoCacheType'] ) ) {
			$this->assertCacheType( $GLOBAL['wgexLingoCacheType'] );
		}

		$this->assertCacheType( $instance->getCacheType() );
	}

	protected function assertCacheType( $cacheType ) {
		$this->assertArrayHasKey( $cacheType, $GLOBALS[ 'wgObjectCaches' ] );
	}

}
