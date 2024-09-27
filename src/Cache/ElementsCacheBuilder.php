<?php

namespace SG\Cache;

use SG\PropertyRegistrationHelper;
use SMW\DataValueFactory;
use SMW\Store;
use SMW\DIProperty;
use SMW\Query\DescriptionFactory;
use SMWPrintRequest as PrintRequest;
use SMWThingDescription as ThingDescription;
use SMWSomeProperty as SomeProperty;
use SMWQuery as Query;
use Lingo\Element;

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
	private $store;

	/* @var GlossaryCache */
	private $glossaryCache;

	private $mDiTerm;
	private $mDiDefinition;
	private $mDiLink;
	private $mDiStyle;

	private $mDvTerm;
	private $mDvDefinition;
	private $mDvLink;
	private $mDvStyle;

	private array $queryResults = [];

	/**
	 * @since  1.1
	 *
	 * @param SMWStore $store
	 * @param GlossaryCache $cache
	 */
	public function __construct( Store $store, GlossaryCache $glossaryCache ) {
		$this->store = $store;
		$this->glossaryCache = $glossaryCache;
	}

	/**
	 * @since 1.1
	 *
	 * @param array $searchTerms
	 * @return array
	 */
	public function getElements( array $searchTerms = [] ) {

		$ret = array();
		$cacheId = substr( md5( implode( '', $searchTerms ) ), 0, 8 );

		if ( !isset( $this->queryResults[ $cacheId ] ) ) {
			$this->queryResults[ $cacheId ] = $this->store->getQueryResult( $this->buildQuery( $searchTerms ) )->getResults();
		}

		// find next line
		$page = current( $this->queryResults[ $cacheId ] );

		if ( $page && count( $ret ) == 0 ) {

			next( $this->queryResults[ $cacheId ] );

			$cachekey = $this->glossaryCache->getKeyForSubject( $page );
			$cachedResult = $this->glossaryCache->getCache()->get( "{$cachekey}_{$cacheId}" );

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
				$this->glossaryCache->getCache()->set( $cachekey, $ret );
			}
		}

		return $ret;
	}

	private function buildElements( $terms, $definition, $link, $style, $page ) {

		$ret = array();

		foreach ( $terms as $term ) {
			$tmp_ret = array(
				Element::ELEMENT_TERM => $term,
				Element::ELEMENT_DEFINITION => $definition,
				Element::ELEMENT_LINK => $link,
				Element::ELEMENT_STYLE => $style,
				Element::ELEMENT_SOURCE => $page
			);

			$ret[] = $tmp_ret;
		}

		return $ret;
	}

	private function buildQuery( array $searchTerms = []) {

		$dataValueFactory = DataValueFactory::getInstance();
		$descriptionFactory = new DescriptionFactory();

		// build term data item and data value for later use
		$this->mDiTerm = new DIProperty( PropertyRegistrationHelper::SG_TERM );
		$this->mDvTerm = $dataValueFactory->newDataValueByType( '_txt' );
		$this->mDvTerm->setProperty( $this->mDiTerm );

		$valueDescriptions = [];
		foreach ( $searchTerms as $searchTerm ) {
			$valueDescriptions[] = $descriptionFactory->newSomeProperty(
				$this->mDiTerm,
				$descriptionFactory->newValueDescription( new \SMWDIBlob( $searchTerm ) )
			);
		}

		$pvTerm = $dataValueFactory->newDataValueByType( '__pro' );
		$pvTerm->setDataItem( $this->mDiTerm );
		$prTerm = new PrintRequest( PrintRequest::PRINT_PROP, null, $pvTerm );

		// build definition data item and data value for later use
		$this->mDiDefinition = new DIProperty( PropertyRegistrationHelper::SG_DEFINITION );
		$this->mDvDefinition = $dataValueFactory->newDataValueByType( '_txt' );
		$this->mDvDefinition->setProperty( $this->mDiDefinition );

		$pvDefinition = $dataValueFactory->newDataValueByType( '__pro' );
		$pvDefinition->setDataItem( $this->mDiDefinition );
		$prDefinition = new PrintRequest( PrintRequest::PRINT_PROP, null, $pvDefinition );

		// build link data item and data value for later use
		$this->mDiLink = new DIProperty( PropertyRegistrationHelper::SG_LINK );
		$this->mDvLink = $dataValueFactory->newDataValueByType( '_txt' );
		$this->mDvLink->setProperty( $this->mDiLink );

		$pvLink = $dataValueFactory->newDataValueByType( '__pro' );
		$pvLink->setDataItem( $this->mDiLink );
		$prLink = new PrintRequest( PrintRequest::PRINT_PROP, null, $pvLink );

		// build style data item and data value for later use
		$this->mDiStyle = new DIProperty( PropertyRegistrationHelper::SG_STYLE );
		$this->mDvStyle = $dataValueFactory->newDataValueByType( '_txt' );
		$this->mDvStyle->setProperty( $this->mDiStyle );

		$pvStyle = $dataValueFactory->newDataValueByType( '__pro' );
		$pvStyle->setDataItem( $this->mDiStyle );
		$prStyle = new PrintRequest( PrintRequest::PRINT_PROP, null, $pvStyle );

		// Create query
		$desc = sizeof( $searchTerms ) === 0
			? new SomeProperty( new DIProperty( '___glt' ), new ThingDescription() )
			: $descriptionFactory->newDisjunction( $valueDescriptions );
		$desc->addPrintRequest( $prTerm );
		$desc->addPrintRequest( $prDefinition );
		$desc->addPrintRequest( $prLink );
		$desc->addPrintRequest( $prStyle );

		$query = new Query( $desc, false, false );
		$query->sort = true;
		$query->sortkeys['___glt'] = 'ASC';

		if ( defined( 'SMWQuery::PROC_CONTEXT' ) ) {
			$query->setOption( Query::PROC_CONTEXT, 'SG.ElementsCacheBuilder' );
		}

		return $query;
	}

	private function getDefinitionValue( $page ) {

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

	private function getLinkValue( $page ) {

		$link  = null;

		$links = $this->store->getPropertyValues( $page, $this->mDiLink );;

		if ( !empty( $links ) ) {
			$this->mDvLink->setDataItem( $links[0] );
			$link = trim( $this->mDvLink->getShortWikiText() );
		}

		return $link;
	}

	private function getStyleValue( $page ) {

		$style  = null;

		$styles = $this->store->getPropertyValues( $page, $this->mDiStyle );;

		if ( !empty( $styles ) ) {
		  $this->mDvStyle->setDataItem( $styles[0] );
		  $style = trim( $this->mDvStyle->getShortWikiText() );
		}

		return $style;
	}

	private function getTerms( $page ) {

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
