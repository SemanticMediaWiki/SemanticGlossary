<?php

namespace SG\Tests;

use SG\LingoBackendAdapter;
use Lingo\MessageLog;
use SG\Cache\ElementsCacheBuilder;

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
 * @author Youri van den Bogert <yvdbogert@archixl.nl>
 */
class LingoBackendAdapterTest extends \PHPUnit\Framework\TestCase {

	private ?MessageLog $lingoMessageLog = null;
	private ?ElementsCacheBuilder $elementsCacheBuilder = null;
	private ?LingoBackendAdapter $lingoBackendAdapter = null;

	public function setUp(): void {
		parent::setUp();

		$this->lingoMessageLog = $this->getMockBuilder( '\Lingo\MessageLog' )
			->disableOriginalConstructor()
			->getMock();

		$this->elementsCacheBuilder = $this->getMockBuilder( '\SG\Cache\ElementsCacheBuilder' )
			->disableOriginalConstructor()
			->getMock();

		$this->lingoBackendAdapter = new LingoBackendAdapter(
			$this->lingoMessageLog,
			$this->elementsCacheBuilder
		);
	}

	public function testCanConstruct() {
		$this->assertInstanceOf(
			'\SG\LingoBackendAdapter',
			new LingoBackendAdapter()
		);
	}

	public function testUseCache() {
		$this->assertFalse( $this->lingoBackendAdapter->useCache() );
	}

	public function testNextOnEmptyElementsResult() {
		$this->elementsCacheBuilder->expects( $this->once() )
			->method( 'getElements' )
			->willReturn( [] );

		$this->assertNull( $this->lingoBackendAdapter->next() );
	}

	public function testSetSearchTerms() {
		$searchTerms = [ 'a', 'ab', 'abc', 'abcd' ];

		// Search terms with a length of 3 or less are ignored
		$this->lingoBackendAdapter->setSearchTerms( $searchTerms );

		$this->assertCount( 2, $this->lingoBackendAdapter->getSearchTerms() );
	}

}
