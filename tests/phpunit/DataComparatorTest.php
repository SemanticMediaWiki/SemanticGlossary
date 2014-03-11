<?php

namespace SG\Tests;

use SG\DataComparator;

/**
 * @covers \SG\DataComparator
 *
 * @ingroup Test
 *
 * @group SG
 * @group SGExtension
 *
 * @licence GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class DataComparatorTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {

		$store = $this->getMockBuilder( 'SMWStore' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$semanticData = $this->getMockBuilder( 'SMWSemanticData' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$instance = new DataComparator( $store, $semanticData );

		$this->assertInstanceOf( '\SG\DataComparator', $instance );
	}

	public function testInspectWithoutData() {

		$store = $this->getMockBuilder( 'SMWStore' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$semanticData = $this->getMockBuilder( 'SMWSemanticData' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$instance = new DataComparator( $store, $semanticData );

		$this->assertFalse( $instance->byPropertyId( 'foo' ) );
	}

}
