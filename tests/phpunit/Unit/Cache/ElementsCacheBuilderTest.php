<?php

namespace SG\Tests\Cache;

use HashBagOStuff;
use Lingo\Element;
use SG\Cache\ElementsCacheBuilder;
use SG\Cache\GlossaryCache;
use SMWDIBlob as DIBlob;
use SMWDIWikiPage as DIWikiPage;
use Title;

/**
 * @covers \SG\Cache\ElementsCacheBuilder
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
class ElementsCacheBuilderTest extends \PHPUnit\Framework\TestCase {

	public function testCanConstruct() {
		$store = $this->getMockBuilder( '\SMW\Store' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$glossaryCache = $this->getMockBuilder( '\SG\Cache\GlossaryCache' )->getMock();

		$this->assertInstanceOf(
			'\SG\Cache\ElementsCacheBuilder',
			new ElementsCacheBuilder( $store, $glossaryCache )
		);
	}

	public function testGetTermsForSingleTermWithDefinitionOnNonCachedResult() {
		$this->markTestSkipped( 'Needs to be fixed with the new version of SG' );

		$page = DIWikiPage::newFromTitle( Title::newFromText( __METHOD__ ) );

		$queryResult = $this->getMockBuilder( '\stdClass' )
			->disableOriginalConstructor()
			->onlyMethods( [ 'getResults' ] )
			->getMock();

		$queryResult->expects( $this->once() )
			->method( 'getResults' )
			->willReturn( [ $page ] );

		$store = $this->getMockBuilder( 'SMWStore' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$store->expects( $this->once() )
			->method( 'getQueryResult' )
			->willReturn( $queryResult );

		// at() position depends on the sequence as to when a method is called

		$store->expects( $this->at( 1 ) )
			->method( 'getPropertyValues' )
			->willReturn( [ new DIBlob( ' Foo term ' ) ] );

		$store->expects( $this->at( 2 ) )
			->method( 'getPropertyValues' )
			->willReturn( [ new DIBlob( ' some Definition ' ) ] );

		$glossaryCache = new GlossaryCache( new HashBagOStuff() );

		$instance = new ElementsCacheBuilder(
			$store,
			$glossaryCache
		);

		$results = $instance->getElements();

		$this->assertEquals(
			$results,
			$glossaryCache->getCache()->get( $glossaryCache->getKeyForSubject( $page ) )
		);

		$this->assertLingoElement(
			'Foo term',
			'some Definition',
			null,
			null,
			$results[0]
		);
	}

	protected function assertLingoElement( $term, $definition, $link, $style, $result ) {
		$this->assertEquals( $term, $result[ Element::ELEMENT_TERM ] );
		$this->assertEquals( $definition, $result[ Element::ELEMENT_DEFINITION ] );
		$this->assertEquals( $link, $result[ Element::ELEMENT_LINK ] );
		$this->assertEquals( $style, $result[ Element::ELEMENT_STYLE ] );

		$this->assertInstanceOf(
			'SMWDIWikiPage',
			$result[ Element::ELEMENT_SOURCE ]
		);
	}

}
