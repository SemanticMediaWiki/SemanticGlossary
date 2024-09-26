<?php

namespace SG\Tests\Maintenance;

use SG\Maintenance\GlossaryCacheRebuilder;
use SG\Cache\GlossaryCache;

use Title;

/**
 * @uses \SG\Maintenance\GlossaryCacheRebuilder
 *
 * @ingroup Test
 *
 * @group SG
 * @group SGExtension
 *
 * @license GNU GPL v2+
 * @since 1.1
 *
 * @author mwjames
 */
class GlossaryCacheRebuilderTest extends \PHPUnit\Framework\TestCase {

	public function testCanConstruct() {

		$this->markTestSkipped(
			'This test should be revised in the next release'
		);

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
