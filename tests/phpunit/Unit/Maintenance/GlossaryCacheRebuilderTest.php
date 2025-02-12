<?php

namespace SG\Tests\Maintenance;

use SG\Maintenance\GlossaryCacheRebuilder;

/**
 * @uses \SG\Maintenance\GlossaryCacheRebuilder
 *
 * @ingroup Test
 *
 * @group SG
 * @group SGExtension
 *
 * @license GPL-2.0-or-later
 * @since 1.1
 *
 * @author mwjames
 */
class GlossaryCacheRebuilderTest extends \PHPUnit\Framework\TestCase {

	public function testCanConstruct() {
		$store = $this->getMockForAbstractClass( '\SMW\Store' );

		$glossaryCache = $this->getMockBuilder( '\SG\Cache\GlossaryCache' )
			->disableOriginalConstructor()
			->getMock();

		$this->assertInstanceOf(
			'\SG\Maintenance\GlossaryCacheRebuilder',
			new GlossaryCacheRebuilder( $store, $glossaryCache )
		);
	}

}
