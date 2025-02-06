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
 * @license GPL-2.0-or-later
 * @since 1.1
 *
 * @author mwjames
 */
class LingoBackendAdapterTest extends \PHPUnit\Framework\TestCase {

	public function testCanConstruct() {
		$this->assertInstanceOf(
			'\SG\LingoBackendAdapter',
			new LingoBackendAdapter()
		);
	}

	public function testNextOnEmptyElementsResult() {
		$this->markTestSkipped( 'Needs to be fixed with the new version of SG' );
    
		$lingoMessageLog = $this->getMockBuilder( '\Lingo\MessageLog' )
			->disableOriginalConstructor()
			->getMock();

		$elementsCacheBuilder = $this->getMockBuilder( '\SG\Cache\ElementsCacheBuilder' )
			->disableOriginalConstructor()
			->getMock();

		$elementsCacheBuilder->expects( $this->once() )
			->method( 'getElements' )
			->willReturn( array() );

		$instance = new LingoBackendAdapter(
			$lingoMessageLog,
			$elementsCacheBuilder
		);

		$instance->next();

		$this->assertTrue( $instance->useCache() );
	}

}
