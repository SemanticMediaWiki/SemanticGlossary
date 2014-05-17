<?php

namespace SG\Cache;

use SG\CacheHelper;

use SMWStore;
use SMWDIProperty;
use SMWStringValue;
use SMWPrintRequest;
use SMWPropertyValue;
use SMWThingDescription;
use SMWSomeProperty;
use SMWQuery;
use LingoElement;

use BagOStuff;

/**
 * @ingroup SG
 * @ingroup SemanticGlossary
 *
 * @license GNU GPL v2+
 * @since 1.1
 *
 * @author Stephan Gambke
 * @author mwjames
 */
class ElementsCacheBuilder {

	/* @var Store */
	protected $store;

	/* @var BagOStuff */
	protected $cache;

	protected $mDiTerm;
	protected $mDiDefinition;
	protected $mDiLink;
	protected $mDiStyle;

	protected $mDvTerm;
	protected $mDvDefinition;
	protected $mDvLink;
	protected $mDvStyle;

	protected $queryResults;

	/**
	 * @since  1.1
	 *
	 * @param SMWStore $store
	 * @param BagOStuff|null $cache
	 */
	public function __construct( SMWStore $store, BagOStuff $cache = null ) {
		$this->store = $store;
		$this->cache = $cache;

		if ( $this->cache === null ) {
			$this->cache = CacheHelper::getCache();
		}
	}

	/**
	 * @since 1.1
	 *
	 * @return array
	 */
	public function getElements() {
		wfProfileIn( __METHOD__ );

		$ret = array();

		if ( $this->queryResults === null ) {
			$this->queryResults = $this->store->getQueryResult( $this->buildQuery() )->getResults();
		}

		// find next line
		$page = current( $this->queryResults );

		if ( $page && count( $ret ) == 0 ) {

			next( $this->queryResults );

			$cachekey = CacheHelper::getKey( $page );
			$cachedResult = $this->cache->get( $cachekey );

			// cache hit?
			if ( $cachedResult !== false && $cachedResult !== null ) {

				wfDebug( "Cache hit: Got glossary entry $cachekey from cache.\n" );
				$ret = &$cachedResult;
			} else {

				wfDebug( "Cache miss: Glossary entry $cachekey not found in cache.\n" );

				$ret = $this->buildElements(
					$this->getTerms( $page ),
					$this->getDefinitionValue( $page ),
					$this->getLinkValue( $page ),
					$this->getStyleValue( $page ),
					$page
				);

				wfDebug( "Cached glossary entry $cachekey.\n" );
				$this->cache->set( $cachekey, $ret );
			}
		}

		wfProfileOut( __METHOD__ );
		return $ret;
	}

	protected function buildElements( $terms, $definition, $link, $style, $page ) {

		$ret = array();

		foreach ( $terms as $term ) {
			$tmp_ret = array(
				LingoElement::ELEMENT_TERM => $term,
				LingoElement::ELEMENT_DEFINITION => $definition,
				LingoElement::ELEMENT_LINK => $link,
				LingoElement::ELEMENT_STYLE => $style,
				LingoElement::ELEMENT_SOURCE => $page
			);

			$ret[] = $tmp_ret;
		}

		return $ret;
	}

	protected function buildQuery() {
		// build term data item and data value for later use
		$this->mDiTerm = new SMWDIProperty( '___glt' );
		$this->mDvTerm = new SMWStringValue( '_str' );
		$this->mDvTerm->setProperty( $this->mDiTerm );

		$pvTerm = new SMWPropertyValue( '__pro' );
		$pvTerm->setDataItem( $this->mDiTerm );
		$prTerm = new SMWPrintRequest( SMWPrintRequest::PRINT_PROP, null, $pvTerm );

		// build definition data item and data value for later use
		$this->mDiDefinition = new SMWDIProperty( '___gld' );
		$this->mDvDefinition = new SMWStringValue( '_txt' );
		$this->mDvDefinition->setProperty( $this->mDiDefinition );

		$pvDefinition = new SMWPropertyValue( '__pro' );
		$pvDefinition->setDataItem( $this->mDiDefinition );
		$prDefinition = new SMWPrintRequest( SMWPrintRequest::PRINT_PROP, null, $pvDefinition );

		// build link data item and data value for later use
		$this->mDiLink = new SMWDIProperty( '___gll' );
		$this->mDvLink = new SMWStringValue( '_str' );
		$this->mDvLink->setProperty( $this->mDiLink );

		$pvLink = new SMWPropertyValue( '__pro' );
		$pvLink->setDataItem( $this->mDiLink );
		$prLink = new SMWPrintRequest( SMWPrintRequest::PRINT_PROP, null, $pvLink );

		// build style data item and data value for later use
		$this->mDiStyle = new SMWDIProperty( '___gls' );
		$this->mDvStyle = new SMWStringValue( '_txt' );
		$this->mDvStyle->setProperty( $this->mDiStyle );

		$pvStyle = new SMWPropertyValue( '__pro' );
		$pvStyle->setDataItem( $this->mDiStyle );
		$prStyle = new SMWPrintRequest( SMWPrintRequest::PRINT_PROP, null, $pvStyle );

		// Create query
		$desc = new SMWSomeProperty( new SMWDIProperty( '___glt' ), new SMWThingDescription() );
		$desc->addPrintRequest( $prTerm );
		$desc->addPrintRequest( $prDefinition );
		$desc->addPrintRequest( $prLink );
		$desc->addPrintRequest( $prStyle );

		$query = new SMWQuery( $desc, false, false );
		$query->sort = true;
		$query->sortkeys['___glt'] = 'ASC';

		return $query;
	}

	protected function getDefinitionValue( $page ) {

		$definition  = null;

		$definitions = $this->store->getPropertyValues(
			$page,
			$this->mDiDefinition
		);

		if ( !empty( $definitions ) ) {
			$this->mDvDefinition->setDataItem( $definitions[0] );
			$definition = trim( $this->mDvDefinition->getShortWikiText() );
		}

		return $definition;
	}

	protected function getLinkValue( $page ) {

		$link  = null;

		$links = $this->store->getPropertyValues( $page, $this->mDiLink );;

		if ( !empty( $links ) ) {
			$this->mDvLink->setDataItem( $links[0] );
			$link = trim( $this->mDvLink->getShortWikiText() );
		}

		return $link;
	}

	protected function getStyleValue( $page ) {

		$style  = null;

		$styles = $this->store->getPropertyValues( $page, $this->mDiStyle );;

		if ( !empty( $styles ) ) {
		  $this->mDvStyle->setDataItem( $styles[0] );
		  $style = trim( $this->mDvStyle->getShortWikiText() );
		}

		return $style;
	}

	protected function getTerms( $page ) {

		$collectedTerms = array();

		$terms = $this->store->getPropertyValues( $page, $this->mDiTerm );

		if ( $terms !== array() ) {
			foreach ( $terms as $term ) {
				$this->mDvTerm->setDataItem( $term );
				$collectedTerms[] = trim( $this->mDvTerm->getShortWikiText() );
			}
		}

		return $collectedTerms;
	}

}
