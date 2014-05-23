<?php

namespace SG\Tests;

use SG\SemanticDataComparator;

/**
 * @covers \SG\SemanticDataComparator
 *
 * @ingroup Test
 *
 * @group SG
 * @group SGExtension
 * @group extension-semantic-glossary
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class SemanticDataComparatorTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {

		$store = $this->getMockBuilder( 'SMWStore' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$semanticData = $this->getMockBuilder( 'SMWSemanticData' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$instance = new SemanticDataComparator( $store, $semanticData );

		$this->assertInstanceOf( '\SG\SemanticDataComparator', $instance );
	}

	public function testInspectWithoutData() {

		$store = $this->getMockBuilder( 'SMWStore' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$semanticData = $this->getMockBuilder( 'SMWSemanticData' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$instance = new SemanticDataComparator( $store, $semanticData );

		$this->assertFalse( $instance->byPropertyId( 'foo' ) );
	}

}
