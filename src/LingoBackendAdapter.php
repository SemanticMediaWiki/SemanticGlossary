<?php

namespace SG;

use SG\Cache\ElementsCacheBuilder;
use SG\Cache\GlossaryCache;

use SMW\StoreFactory;

use Lingo\Backend;
use Lingo\MessageLog;

/**
 * @ingroup SG
 * @ingroup SemanticGlossary
 *
 * @license GNU GPL v2+
 * @since 1.1
 *
 * @author mwjames
 */
class LingoBackendAdapter extends Backend {

	/* @var ElementsCacheBuilder */
	protected $elementsCacheBuilder = null;

	protected $elements = array();

	/**
	 * @since 1.1
	 *
	 * @param MessageLog|null &$messages
	 * @param ElementsCacheBuilder|null $elementsCacheBuilder
	 */
	public function __construct( MessageLog &$messages = null, ElementsCacheBuilder $elementsCacheBuilder = null ) {
		parent::__construct( $messages );
		$this->elementsCacheBuilder = $elementsCacheBuilder;

		if ( $this->elementsCacheBuilder === null ) {
			$this->elementsCacheBuilder = new ElementsCacheBuilder(
				StoreFactory::getStore(),
				new GlossaryCache()
			);
		}
	}

	/**
	 * This function returns the next element. The element is an array of four
	 * strings: Term, Definition, Link, Source, Style. If there is no next element
	 * the function returns null.
	 *
	 * @since  1.1
	 *
	 * @return array|null the next element or null
	 */
	public function next() {

		if ( $this->elements === array() ) {
			$this->elements = $this->elementsCacheBuilder->getElements( $this->getSearchTerms() );
		}

		return array_pop( $this->elements );
	}

	/**
	 * This backend doesn't use caching, since we do specific queries for glossary 
	 * terms. This was previously set to true, since the whole glossary would be 
	 * queried upon. 
	 *
	 * @since  5.0
	 *
	 * @return boolean
	 */
	public function useCache() {
		return false;
	}

}
