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
 * @license GPL-2.0-or-later
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
	 * This backend is cache-enabled so this function returns true.
	 *
	 * Actual caching is done by the parser, the backend just calls
	 * Parser::purgeCache when necessary.
	 *
	 * @since  1.1
	 *
	 * @return bool
	 */
	public function useCache() {
		return true;
	}

}
