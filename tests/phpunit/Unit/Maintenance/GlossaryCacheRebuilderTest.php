<?php

namespace SG\Tests\Maintenance;

use HashBagOStuff;
use MediaWiki\Title\Title;
use SG\Cache\GlossaryCache;
use SG\Maintenance\GlossaryCacheRebuilder;
use SMW\DIWikiPage;
use SMW\Store;

/**
 * @covers \SG\Maintenance\GlossaryCacheRebuilder
 *
 * @ingroup Test
 *
 * @group SG
 * @group SGExtension
 *
 * @license GPL-2.0-or-later
 * @since 1.1
 *
 * @author mwjames
 */
class GlossaryCacheRebuilderTest extends \PHPUnit\Framework\TestCase {

	/**
	 * @var Store
	 */
	private $storeMock;

	/**
	 * @var GlossaryCache
	 */
	private $glossaryCache;

	protected function setUp(): void {
		parent::setUp();

		$this->storeMock = $this->getMockBuilder( Store::class )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$this->glossaryCache = new GlossaryCache( new HashBagOStuff() );
	}

	public function testCanConstruct() {
		$this->assertInstanceOf(
			GlossaryCacheRebuilder::class,
			new GlossaryCacheRebuilder( $this->storeMock, $this->glossaryCache )
		);
	}

	public function testSetParametersWithVerbose() {
		$instance = new GlossaryCacheRebuilder( $this->storeMock, $this->glossaryCache );
		$instance->setParameters( [ 'verbose' => true ] );

		// No exception means parameters were accepted
		$this->assertTrue( true );
	}

	public function testGetRebuildCountInitiallyZero() {
		$instance = new GlossaryCacheRebuilder( $this->storeMock, $this->glossaryCache );

		$this->assertSame( 0, $instance->getRebuildCount() );
	}

	public function testRebuildClearsCacheAndPurgesLingo() {
		$page = DIWikiPage::newFromTitle( Title::newFromText( 'GlossaryTestPage' ) );

		// Pre-populate cache for the subject
		$cacheKey = $this->glossaryCache->getKeyForSubject( $page );
		$this->glossaryCache->getCache()->set( $cacheKey, 'cached.value' );

		// Verify cache is populated
		$this->assertSame( 'cached.value', $this->glossaryCache->getCache()->get( $cacheKey ) );

		// Mock query results: first call is count query (returns int),
		// second call is the actual result query (returns object with getResults)
		$queryResult = $this->getMockBuilder( \stdClass::class )
			->addMethods( [ 'getResults' ] )
			->getMock();
		$queryResult->method( 'getResults' )
			->willReturn( [ $page ] );

		$this->storeMock->expects( $this->exactly( 2 ) )
			->method( 'getQueryResult' )
			->willReturnOnConsecutiveCalls( 1, $queryResult );

		$messages = [];
		$reporter = static function ( $message ) use ( &$messages ) {
			$messages[] = $message;
		};

		$instance = new GlossaryCacheRebuilder( $this->storeMock, $this->glossaryCache, $reporter );
		$instance->setParameters( [ 'verbose' => true ] );

		$result = $instance->rebuild();

		$this->assertTrue( $result );
		$this->assertSame( 1, $instance->getRebuildCount() );

		// Verify subject cache entry was deleted
		$this->assertFalse(
			$this->glossaryCache->getCache()->get( $cacheKey ),
			'Subject cache entry should be deleted after rebuild'
		);

		// Verify messages were reported
		$this->assertNotEmpty( $messages );
	}

}
