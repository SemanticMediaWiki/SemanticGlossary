<?php

namespace SG\Tests;

use SG\SemanticDataComparator;

/**
 * @covers \SG\SemanticDataComparator
 * @group extension-semantic-glossary
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class SemanticDataComparatorTest extends \PHPUnit_Framework_TestCase {

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
			->will( $this->returnValue( array() ) );

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
