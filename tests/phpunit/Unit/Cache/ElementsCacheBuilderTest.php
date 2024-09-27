<?php

namespace SG\Tests\Cache;

use SG\Cache\ElementsCacheBuilder;
use SG\Cache\GlossaryCache;
use Lingo\Element;
use SMWDIWikiPage as DIWikiPage;
use SMWDIBlob as DIBlob;
use Title;
use HashBagOStuff;

/**
 * @covers \SG\Cache\ElementsCacheBuilder
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
		$page = DIWikiPage::newFromTitle( Title::newFromText( __METHOD__ ) );

		$queryResult = $this->getMockBuilder( '\stdClass' )
			->disableOriginalConstructor()
			->setMethods( array( 'getResults' ) )
			->getMock();

		$queryResult->expects( $this->once() )
			->method( 'getResults' )
			->will( $this->returnValue( array( $page ) ) );

		$store = $this->getMockBuilder( 'SMWStore' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$store->expects( $this->once() )
			->method( 'getQueryResult' )
			->will( $this->returnValue( $queryResult ) );

		// at() position depends on the sequence as to when a method is called

		$store->expects( $this->at( 1 ) )
			->method( 'getPropertyValues' )
			->will( $this->returnValue( array( new DIBlob( ' Foo term ' ) ) ) );

		$store->expects( $this->at( 2 ) )
			->method( 'getPropertyValues' )
			->will( $this->returnValue( array( new DIBlob( ' some Definition ' ) ) ) );

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
