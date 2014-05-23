<?php

namespace SG;

use SG\Cache\ElementsCacheBuilder;
use SG\Cache\GlossaryCache;

use SMW\StoreFactory;

use LingoBackend;
use LingoMessageLog;

/**
 * @ingroup SG
 * @ingroup SemanticGlossary
 *
 * @license GNU GPL v2+
 * @since 1.1
 *
 * @author mwjames
 */
class LingoBackendAdapter extends LingoBackend {

	/* @var ElementsCacheBuilder */
	protected $elementsCacheBuilder = null;

	protected $elements = array();

	/**
	 * @since 1.1
	 *
	 * @param LingoMessageLog|null &$messages
	 * @param ElementsCacheBuilder|null $elementsCacheBuilder
	 */
	public function __construct( LingoMessageLog &$messages = null, ElementsCacheBuilder $elementsCacheBuilder = null ) {
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
	 * @return the next element or null
	 */
	public function next() {

		wfProfileIn( __METHOD__ );

		if ( $this->elements === array() ) {
			$this->elements = $this->elementsCacheBuilder->getElements();
		}

		wfProfileOut( __METHOD__ );
		return array_pop( $this->elements );
	}

	/**
	 * This backend is cache-enabled so this function returns true.
	 *
	 * Actual caching is done by the parser, the backend just calls
	 * LingoParser::purgeCache when necessary.
	 *
	 * @since  1.1
	 *
	 * @return boolean
	 */
	public function useCache() {
		return true;
	}

}
