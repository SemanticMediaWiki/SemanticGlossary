<?php

namespace SG\Tests;

use SG\SemanticDataComparator;

/**
 * @covers \SG\SemanticDataComparator
 * @group extension-semantic-glossary
 *
 * @license GPL-2.0-or-later
 * @since 1.0
 *
 * @author mwjames
 */
class SemanticDataComparatorTest extends \PHPUnit\Framework\TestCase {

	public function testCanConstruct() {
		$store = $this->getMockBuilder( '\SMW\Store' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$semanticData = $this->getMockBuilder( '\SMW\SemanticData' )
			->disableOriginalConstructor()
			->getMock();

		$instance = new SemanticDataComparator(
			$store,
			$semanticData
		);

		$this->assertInstanceOf(
			'\SG\SemanticDataComparator',
			$instance
		);
	}

	/**
	 * @dataProvider propertyIdProvider
	 */
	public function testInspectForEmptyData( $propertyId ) {
		$store = $this->getMockBuilder( '\SMW\Store' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$store->method( 'getPropertyValues' )
			->willReturn( [] );

		$semanticData = $this->getMockBuilder( '\SMW\SemanticData' )
			->disableOriginalConstructor()
			->getMock();

		$semanticData->expects( $this->once() )
			->method( 'getProperties' )
			->willReturn( array() );

		$instance = new SemanticDataComparator(
			$store,
			$semanticData
		);

		$this->assertFalse(
			$instance->compareForProperty( $propertyId )
		);
	}

	public function propertyIdProvider() {
		$provider = array(
			array( 'Foo' ),
			array( '__Foo' )
		);

		return $provider;
	}

}
