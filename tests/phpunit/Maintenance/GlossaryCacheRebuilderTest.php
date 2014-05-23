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
class GlossaryCacheRebuilderTest extends \PHPUnit_Framework_TestCase {

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

	public function testRebuildPagesThatContainDuplicateEntity() {

		$subject = $this->getMockBuilder( '\SMW\DIWikiPage' )
			->disableOriginalConstructor()
			->getMock();

		$subject->expects( $this->exactly( 2 ) )
			->method( 'getTitle' )
			->will( $this->returnValue( Title::newFromText( __METHOD__ ) ) );

		$queryResult = $this->getMockBuilder( '\SMWQueryResult' )
			->disableOriginalConstructor()
			->getMock();

		$queryResult->expects( $this->once() )
			->method( 'getResults' )
			->will( $this->returnValue( array( $subject, $subject ) ) );

		$store = $this->getMockBuilder( '\SMW\Store' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$store->expects( $this->at( 1 ) )
			->method( 'getQueryResult' )
			->will( $this->returnValue( $queryResult ) );

		$bagOStuff = $this->getMockBuilder( 'BagOStuff' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$bagOStuff->expects( $this->at( 0 ) )
			->method( 'delete' )
			->with( $this->stringContains( 'lingotree' ) );

		$bagOStuff->expects( $this->at( 1 ) )
			->method( 'delete' )
			->with( $this->stringContains( 'semanticglossary' ) );

		$bagOStuff->expects( $this->at( 2 ) )
			->method( 'delete' )
			->with( $this->stringContains( 'semanticglossary' ) );

		$instance = new GlossaryCacheRebuilder(
			$store,
			new GlossaryCache( $bagOStuff )
		);

		$instance->setParameters( array() );

		$this->assertTrue( $instance->rebuild() );

		$this->assertEquals(
			1,
			$instance->getRebuildCount(),
			'Asserts that rebuild is counted only once because the duplicate entity was removed'
		);
	}

}
