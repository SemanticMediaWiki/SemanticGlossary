<?php

namespace SG\Cache;

use SMW\DIWikiPage;
use ObjectCache;
use BagOStuff;

/**
 * @ingroup SG
 * @ingroup SemanticGlossary
 *
 * @license GPL-2.0-or-later
 * @since 1.0
 *
 * @author mwjames
 */
class GlossaryCache {

	/* @var BagOstuff */
	private $cache = null;

	/**
	 * @since 1.1
	 *
	 * @param BagOStuff|null $cache
	 */
	public function __construct( BagOStuff $cache = null ) {
		$this->cache = $cache;
	}

	/**
	 * @since 1.0
	 *
	 * @return BagOStuff
	 */
	public function getCache() {
		if ( $this->cache === null ) {
			$this->cache = ObjectCache::getInstance( self::getCacheType() );
		}

		return $this->cache;
	}

	/**
	 * @since 1.0
	 *
	 * @param DIWikiPage $subject
	 *
	 * @return string
	 */
	public function getKeyForSubject( DIWikiPage $subject ) {
		return $this->getCache()->makeKey( 'ext', 'semanticglossary', $subject->getSerialization() );
	}

	/**
	 * @since 1.1
	 *
	 * @return string
	 */
	public function getKeyForLingo() {
        return $this->getCache()->makeKey( 'ext', 'lingo', 'lingotree' );
	}

	/**
	 * @since 1.0
	 *
	 * @return string
	 */
	public function getCacheType() {
		if ( isset( $GLOBAL['wgexLingoCacheType'] ) && $GLOBAL['wgexLingoCacheType'] !== null ) {
			return $GLOBAL['wgexLingoCacheType'];
		}

		return $GLOBALS['wgMainCacheType'];
	}

}
