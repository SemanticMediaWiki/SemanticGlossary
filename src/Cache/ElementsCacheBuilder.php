<?php

namespace SG\Cache;

use Lingo\Element;
use SG\PropertyRegistrationHelper;
use SMW\DataValueFactory;
use SMW\DIProperty;
use SMW\DIWikiPage;
use SMW\Query\DescriptionFactory;
use SMW\Store;
use SMW\Query\PrintRequest;
use SMWQuery as Query;
use SMW\Query\Language\SomeProperty;
use SMW\Query\Language\ThingDescription;

/**
 * @ingroup SG
 * @ingroup SemanticGlossary
 *
 * @license GPL-2.0-or-later
 * @since 1.1
 *
 * @author Stephan Gambke
 * @author mwjames
 */
class ElementsCacheBuilder {

	/**
	 * @var SMWStore|Store
	 */
	private $store;

	/**
	 * @var GlossaryCache
	 */
	private $glossaryCache;

	/**
	 * @var DIProperty
	 */
	private $mDiTerm;

	/**
	 * @var DIProperty
	 */
	private $mDiDefinition;

	/**
	 * @var DIProperty
	 */
	private $mDiLink;

	/**
	 * @var DIProperty
	 */
	private $mDiStyle;

	/**
	 * @var \SMWDataValue
	 */
	private $mDvTerm;

	/**
	 * @var \SMWDataValue
	 */
	private $mDvDefinition;

	/**
	 * @var \SMWDataValue
	 */
	private $mDvLink;

	/**
	 * @var \SMWDataValue
	 */
	private $mDvStyle;

	/**
	 * @var array
	 */
	private array $queryResults = [];

	/**
	 * @var array
	 */
	private array $registeredTerms = [];

	/**
	 * @var int
	 */
	private int $batchSize = 5;

	/**
	 * @param Store $store
	 * @param GlossaryCache $glossaryCache
	 * @since  1.1
	 */
	public function __construct( Store $store, GlossaryCache $glossaryCache ) {
		$this->store = $store;
		$this->glossaryCache = $glossaryCache;
	}

	/**
	 * @param array $searchTerms
	 * @return array
	 * @since 1.1
	 *
	 */
	public function getElements( array $searchTerms = [] ) {
		$ret = [];
		$batches = array_chunk( $searchTerms, $this->batchSize );

		foreach ( $batches as $batch ) {
			$cacheId = substr( md5( implode( '', $batch ) ), 0, 8 );

			if ( !isset( $this->queryResults[$cacheId] ) ) {
				$query = $this->buildQuery( $batch );
				$this->queryResults[$cacheId] = $this->store->getQueryResult( $query )->getResults();
			}

			/**
			 * @var DIWikiPage $page
			 */
			foreach ( $this->queryResults[$cacheId] as $page ) {
				$cachekey = $this->glossaryCache->getKeyForSubject( $page );
				$cachedResult = $this->glossaryCache->getCache()->get( "{$cachekey}_{$cacheId}" );

				// Cache hit?
				if ( $cachedResult !== false && $cachedResult !== null ) {
					wfDebug( "Cache hit: Got glossary entry $cachekey from cache.\n" );
					$ret = array_merge( $ret, $cachedResult );
				} else {
					wfDebug( "Cache miss: Glossary entry $cachekey not found in cache.\n" );

					$elements = $this->buildElements(
						$this->getTerms( $page ),
						$this->getDefinitionValue( $page ),
						$this->getLinkValue( $page ),
						$this->getStyleValue( $page ),
						$page
					);

					wfDebug( "Cached glossary entry $cachekey.\n" );
					$this->glossaryCache->getCache()->set( $cachekey, $elements );

					$ret = array_merge( $ret, $elements );
				}
			}
		}

		return $ret;
	}

	/**
	 * @param string $terms
	 * @param string $definition
	 * @param string $link
	 * @param string $style
	 * @param string $page
	 *
	 * @return array
	 */
	private function buildElements( $terms, $definition, $link, $style, $page ): array {
		$ret = [];

		foreach ( $terms as $term ) {
			$uuid = substr( md5( $term . $definition . $link ), 0, 8 );
			if ( in_array( $uuid, $this->registeredTerms ) ) {
				continue;
			}

			$tmp_ret = [
				Element::ELEMENT_TERM => $term,
				Element::ELEMENT_DEFINITION => $definition,
				Element::ELEMENT_LINK => $link,
				Element::ELEMENT_STYLE => $style,
				Element::ELEMENT_SOURCE => $page
			];
			$ret[] = $tmp_ret;

			// We register the term to avoid duplicates
			$this->registeredTerms[] = $uuid;
		}

		return $ret;
	}

	/**
	 * Build a query to get the glossary elements
	 *
	 * @param array $searchTerms
	 *
	 * @return Query
	 */
	private function buildQuery( array $searchTerms = [] ) {
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
				$descriptionFactory->newValueDescription(
					new \SMWDIBlob( $searchTerm ),
					null,
					SMW_CMP_LIKE
				)
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
		$desc = count( $searchTerms ) === 0
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

	/**
	 * Retrieve the definition value from the page
	 *
	 * @param DIWikiPage $page
	 *
	 * @return string|null
	 */
	private function getDefinitionValue( DIWikiPage $page ): ?string {
		$definition = null;

		$definitions = $this->store->getPropertyValues( $page, $this->mDiDefinition );

		if ( !empty( $definitions ) ) {
			$this->mDvDefinition->setDataItem( $definitions[0] );
			$definition = trim( $this->mDvDefinition->getShortWikiText() );
		}

		return $definition;
	}

	/**
	 * Retrieve the link value from the page
	 *
	 * @param DIWikiPage $page
	 *
	 * @return string|null
	 */
	private function getLinkValue( DIWikiPage $page ): ?string {
		$link = null;

		$links = $this->store->getPropertyValues( $page, $this->mDiLink );

		if ( !empty( $links ) ) {
			$this->mDvLink->setDataItem( $links[0] );
			$link = trim( $this->mDvLink->getShortWikiText() );
		}

		return $link;
	}

	/**
	 * Retrieve the style value from the page
	 *
	 * @param DIWikiPage $page
	 *
	 * @return string|null
	 */
	private function getStyleValue( DIWikiPage $page ) {
		$style = null;

		$styles = $this->store->getPropertyValues( $page, $this->mDiStyle );

		if ( !empty( $styles ) ) {
			$this->mDvStyle->setDataItem( $styles[0] );
			$style = trim( $this->mDvStyle->getShortWikiText() );
		}

		return $style;
	}

	/**
	 * Retrieve the terms from the page
	 *
	 * @param DIWikiPage $page
	 *
	 * @return array
	 */
	private function getTerms( DIWikiPage $page ) {
		$collectedTerms = [];

		$terms = $this->store->getPropertyValues( $page, $this->mDiTerm );

		if ( $terms !== [] ) {
			foreach ( $terms as $term ) {
				$this->mDvTerm->setDataItem( $term );
				$collectedTerms[] = trim( $this->mDvTerm->getShortWikiText() );
			}
		}

		return $collectedTerms;
	}

}
