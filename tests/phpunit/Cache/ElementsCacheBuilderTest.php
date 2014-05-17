<?php

namespace SG\Tests\Cache;

use SG\Cache\ElementsCacheBuilder;
use SG\CacheHelper;

use LingoElement;

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
class ElementsCacheBuilderTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {

		$store = $this->getMockBuilder( 'SMWStore' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$this->assertInstanceOf(
			'\SG\Cache\ElementsCacheBuilder',
			new ElementsCacheBuilder( $store )
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

		$cache = new HashBagOStuff();

		$instance = new ElementsCacheBuilder( $store, $cache );

		$results = $instance->getElements();

		$this->assertEquals(
			$results,
			$cache->get( CacheHelper::getKey( $page ) )
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

		$this->assertEquals( $term, $result[ LingoElement::ELEMENT_TERM ] );
		$this->assertEquals( $definition, $result[ LingoElement::ELEMENT_DEFINITION ] );
		$this->assertEquals( $link, $result[ LingoElement::ELEMENT_LINK ] );
		$this->assertEquals( $style, $result[ LingoElement::ELEMENT_STYLE ] );

		$this->assertInstanceOf(
			'SMWDIWikiPage',
			$result[ LingoElement::ELEMENT_SOURCE ]
		);
	}

}
