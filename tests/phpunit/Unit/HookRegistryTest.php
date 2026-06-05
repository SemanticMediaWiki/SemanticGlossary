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

	public function testOnInitPropertiesRegistersProperties() {
		$propertyRegistry = $this->getMockBuilder( '\SMW\PropertyRegistry' )
			->disableOriginalConstructor()
			->getMock();

		$propertyRegistry
			->expects( $this->exactly( 4 ) )
			->method( 'registerProperty' );

		$propertyRegistry
			->expects( $this->exactly( 4 ) )
			->method( 'registerPropertyAlias' );

		$this->assertTrue(
			HookRegistry::onInitProperties( $propertyRegistry )
		);
	}

	/**
	 * @dataProvider cacheInvalidationHandlerProvider
	 *
	 * @param string $handler
	 */
	public function testCacheInvalidationHandlersAreCallable( $handler ) {
		$this->assertTrue(
			is_callable( [ HookRegistry::class, $handler ] )
		);
	}

	/**
	 * @return string[][]
	 */
	public function cacheInvalidationHandlerProvider() {
		return [
			[ 'onBeforeDataUpdateComplete' ],
			[ 'onAfterDeleteSubjectComplete' ],
			[ 'onPageMoveComplete' ],
		];
	}

}
