<?php

namespace SG\Tests;

use SG\HookRegistry;
use SMW\DIProperty;

/**
 * @covers \SG\HookRegistry
 * @group semantic-glossary
 *
 * @license GNU GPL v2+
 * @since 1.2
 *
 * @author mwjames
 */
class HookRegistryTest extends \PHPUnit_Framework_TestCase {

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
