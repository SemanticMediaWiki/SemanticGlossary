<?php

namespace SG\Tests;

use SG\HookRegistry;

/**
 * @covers \SG\HookRegistry
 * @group semantic-glossary
 *
 * @license GPL-2.0-or-later
 * @since 1.2
 *
 * @author mwjames
 */
class HookRegistryTest extends \PHPUnit\Framework\TestCase {

	public function testCanConstruct() {
		$store = $this->getMockBuilder( '\SMW\Store' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$this->assertInstanceOf(
			'\SG\HookRegistry',
			new HookRegistry( $store )
		);
	}

}
