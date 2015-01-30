<?php

namespace SG\Tests;

use SG\LingoBackendAdapter;

/**
 * @covers \SG\LingoBackendAdapter
 *
 * @ingroup Test
 *
 * @group SG
 * @group SGExtension
 * @group extension-semantic-glossary
 *
 * @license GNU GPL v2+
 * @since 1.1
 *
 * @author mwjames
 */
class LingoBackendAdapterTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {

		$this->assertInstanceOf(
			'\SG\LingoBackendAdapter',
			new LingoBackendAdapter()
		);
	}

	public function testNextOnEmptyElementsResult() {

		$lingoMessageLog = $this->getMockBuilder( '\LingoMessageLog' )
			->disableOriginalConstructor()
			->getMock();

		$elementsCacheBuilder = $this->getMockBuilder( '\SG\Cache\ElementsCacheBuilder' )
			->disableOriginalConstructor()
			->getMock();

		$elementsCacheBuilder->expects( $this->once() )
			->method( 'getElements' )
			->will( $this->returnValue( array() ) );

		$instance = new LingoBackendAdapter(
			$lingoMessageLog,
			$elementsCacheBuilder
		);

		$instance->next();

		$this->assertTrue( $instance->useCache() );
	}

}
