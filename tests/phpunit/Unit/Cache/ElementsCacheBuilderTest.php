<?php

namespace SG\Tests\Cache;

use HashBagOStuff;
use Lingo\Element;
use PHPUnit\Framework\TestCase;
use SG\Cache\ElementsCacheBuilder;
use SG\Cache\GlossaryCache;
use SMW\DIWikiPage;
use SMW\Store;
use SMWDIBlob as DIBlob;
use stdClass;
use Title;

/**
 * @covers \SG\Cache\ElementsCacheBuilder
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
class ElementsCacheBuilderTest extends TestCase {
	/**
	 * @const int
	 */
	private const BATCH_SIZE = 5;

	/**
	 * @var Store
	 */
	private $storeMock;

	/**
	 * @var GlossaryCache
	 */
	private $glossaryCache;

	/**
	 * @var ElementsCacheBuilder
	 */
	private $instance;

	protected function setUp(): void {
		parent::setUp();

		$this->storeMock = $this->getMockBuilder( Store::class )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$this->glossaryCache = new GlossaryCache( new HashBagOStuff() );
		$this->instance = new ElementsCacheBuilder( $this->storeMock, $this->glossaryCache );
	}

	public function testConstructorCreatesValidInstance() {
		$this->assertInstanceOf(
			ElementsCacheBuilder::class,
			$this->instance
		);
	}

	/**
	 * @dataProvider provideNullAndEmptyValues
	 */
	public function testGetElementsReturnsEmptyArrayForNullOrEmptyValues( array $propertyValues ) {
		$page = $this->createMockWikiPage();
		$searchTerms = [ 'test_term' ];

		$this->setupQueryResultMock( $page, 1 );
		$this->setupPropertyValuesMock( $propertyValues );

		$results = $this->instance->getElements( $searchTerms );

		$this->assertSame(
			[],
			$results,
			'Should return empty array when values are null or empty'
		);
	}

	public function testGetElementsWithCacheBatching() {
		$page = $this->createMockWikiPage();
		$searchTerms = $this->generateSearchTerms( 6 );

		$this->setupQueryResultMock( $page, 2 );
		$this->setupCacheForFirstBatch( $page, $searchTerms );
		$this->setupPropertyValuesForSecondBatch();

		$results = $this->instance->getElements( $searchTerms );

		$this->assertCount(
			2,
			$results,
			'Should return elements from both cache and store'
		);
		$this->assertLingoElement( 'CachedTerm', 'CachedDefinition', 'CachedLink', 'CachedStyle', $results[0] );
		$this->assertLingoElement( 'Term2', 'Definition2', 'Link2', 'Style2', $results[1] );
	}

	/**
	 * Tests if getElements correctly finds terms using SMW_CMP_LIKE (e.g., containing spaces).
	 */
	public function testGetElementsFindsTermsWithLikeComparison() {
		$page = $this->createMockWikiPage();
		$searchTerm = 'term with space';
		$fullTerm = 'the term with space';
		$definition = 'Test Definition';
		$link = 'Test Link';
		$style = 'Test Style';

		// Expect 1 call to getQueryResult as we only have one search term (no batching needed)
		$this->setupQueryResultMock( $page, 1 );

		// Mock the property values returned for the page
		$this->setupPropertyValuesMock( [
			[ new DIBlob( $fullTerm ) ],
			[ new DIBlob( $definition ) ],
			[ new DIBlob( $link ) ],
			[ new DIBlob( $style ) ]
		] );

		// Execute the method under test
		$results = $this->instance->getElements( [ $searchTerm ] );

		// Assertions
		$this->assertCount( 1, $results, 'Should return exactly one element' );
		$this->assertLingoElement(
			$fullTerm,
			$definition,
			$link,
			$style,
			$results[0]
		);
	}

	public function testGetElementsReturnsEmptyArrayForEmptySearchTerms() {
		$results = $this->instance->getElements( [] );

		$this->assertSame(
			[],
			$results,
			'Should not return elements when terms list is empty'
		);
	}

	/**
	 * @param string $term
	 * @param string $definition
	 * @param string $link
	 * @param string $style
	 * @param array $result
	 */
	private function assertLingoElement( $term, $definition, $link, $style, array $result ) {
		$this->assertEquals( $term, $result[ Element::ELEMENT_TERM ] );
		$this->assertEquals( $definition, $result[ Element::ELEMENT_DEFINITION ] );
		$this->assertEquals( $link, $result[ Element::ELEMENT_LINK ] );
		$this->assertEquals( $style, $result[ Element::ELEMENT_STYLE ] );
		$this->assertInstanceOf(
			DIWikiPage::class,
			$result[ Element::ELEMENT_SOURCE ]
		);
	}

	/**
	 * @return array[]
	 */
	public function provideNullAndEmptyValues() {
		return [
			'all empty arrays' => [ [ [], [], [], [] ] ],
			'null values' => [ [ [], [ new DIBlob( null ) ], [], [] ] ],
			'empty strings' => [ [ [], [], [], [ new DIBlob( '' ) ] ] ],
		];
	}

	/**
	 * @return DIWikiPage
	 */
	private function createMockWikiPage() {
		return DIWikiPage::newFromTitle( Title::newFromText( __METHOD__ ) );
	}

	/**
	 * @param DIWikiPage $page
	 * @param int $expectedCalls
	 */
	private function setupQueryResultMock( DIWikiPage $page, $expectedCalls ) {
		$queryResult = $this->getMockBuilder( stdClass::class )
			->addMethods( [ 'getResults' ] )
			->getMock();

		$queryResult->expects( $this->exactly( $expectedCalls ) )
			->method( 'getResults' )
			->willReturn( [ $page ] );

		$this->storeMock->expects( $this->exactly( $expectedCalls ) )
			->method( 'getQueryResult' )
			->willReturn( $queryResult );
	}

	/**
	 * @param array $sequence
	 */
	private function setupPropertyValuesMock( array $sequence ) {
		$this->storeMock->expects( $this->exactly( count( $sequence ) ) )
			->method( 'getPropertyValues' )
			->willReturnCallback( static function () use ( &$sequence ) {
				return array_shift( $sequence );
			} );
	}

	/**
	 * @param int $count
	 * @return array
	 */
	private function generateSearchTerms( $count ) {
		$terms = [];
		for ( $i = 1; $i <= $count; $i++ ) {
			$terms[] = "term{$i}";
		}
		return $terms;
	}

	/**
	 * @param DIWikiPage $page
	 * @param array $searchTerms
	 */
	private function setupCacheForFirstBatch( DIWikiPage $page, array $searchTerms ) {
		$firstBatchTerms = array_slice( $searchTerms, 0, self::BATCH_SIZE );
		$cacheId = substr( md5( implode( '', $firstBatchTerms ) ), 0, 8 );
		$cacheKey = $this->glossaryCache->getKeyForSubject( $page ) . '_' . $cacheId;

		$cachedData = [ [
			Element::ELEMENT_TERM => 'CachedTerm',
			Element::ELEMENT_DEFINITION => 'CachedDefinition',
			Element::ELEMENT_LINK => 'CachedLink',
			Element::ELEMENT_STYLE => 'CachedStyle',
			Element::ELEMENT_SOURCE => $page
		] ];

		$this->glossaryCache->getCache()->set( $cacheKey, $cachedData );
	}

	/**
	 * Sets up property values for the second batch of tests
	 */
	private function setupPropertyValuesForSecondBatch() {
		$sequence = [
			[ new DIBlob( 'Term2' ) ],
			[ new DIBlob( 'Definition2' ) ],
			[ new DIBlob( 'Link2' ) ],
			[ new DIBlob( 'Style2' ) ]
		];

		$this->setupPropertyValuesMock( $sequence );
	}
}
