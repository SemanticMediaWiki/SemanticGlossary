<?php

namespace SG\Tests\Maintenance;

use HashBagOStuff;
use MediaWiki\Title\Title;
use SG\Cache\GlossaryCache;
use SG\Maintenance\GlossaryCacheRebuilder;
use SMW\DataItems\WikiPage;
use SMW\MediaWiki\JobFactory;
use SMW\MediaWiki\Jobs\UpdateJob;
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

	/**
	 * @var JobFactory
	 */
	private $jobFactory;

	protected function setUp(): void {
		parent::setUp();

		$this->storeMock = $this->getMockBuilder( Store::class )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$this->glossaryCache = new GlossaryCache( new HashBagOStuff() );

		$this->jobFactory = $this->getMockBuilder( JobFactory::class )
			->disableOriginalConstructor()
			->getMock();
	}

	public function testCanConstruct() {
		$this->assertInstanceOf(
			GlossaryCacheRebuilder::class,
			new GlossaryCacheRebuilder( $this->storeMock, $this->glossaryCache, $this->jobFactory )
		);
	}

	public function testSetParametersWithVerbose() {
		$instance = new GlossaryCacheRebuilder( $this->storeMock, $this->glossaryCache, $this->jobFactory );
		$instance->setParameters( [ 'verbose' => true ] );

		// No exception means parameters were accepted
		$this->assertTrue( true );
	}

	public function testGetRebuildCountInitiallyZero() {
		$instance = new GlossaryCacheRebuilder( $this->storeMock, $this->glossaryCache, $this->jobFactory );

		$this->assertSame( 0, $instance->getRebuildCount() );
	}

	public function testRebuildClearsCacheAndPurgesLingo() {
		$page = WikiPage::newFromTitle( Title::newFromText( 'GlossaryTestPage' ) );

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

		// The rebuild queues an SMW update job per processed page; stub the
		// factory so the unit test does not run a real job against the store.
		$updateJob = $this->getMockBuilder( UpdateJob::class )
			->disableOriginalConstructor()
			->getMock();
		$updateJob->method( 'run' )->willReturn( true );

		$this->jobFactory->method( 'newUpdateJob' )
			->willReturn( $updateJob );

		$messages = [];
		$reporter = static function ( $message ) use ( &$messages ) {
			$messages[] = $message;
		};

		$instance = new GlossaryCacheRebuilder( $this->storeMock, $this->glossaryCache, $this->jobFactory, $reporter );
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
