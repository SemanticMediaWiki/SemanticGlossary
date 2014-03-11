<?php

namespace SG;

use ObjectCache;
use BagOStuff;

/**
 * @ingroup SG
 *
 * @licence GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class CacheHelper {

	/**
	 * @since 1.0
	 *
	 * @return BagOStuff
	 */
	public static function getCache() {
		return ObjectCache::getInstance( self::getCacheType() );
	}

	/**
	 * @since 1.0
	 *
	 * @param SMWDIWikiPage $subject
	 *
	 * @return string
	 */
	public static function getKey( \SMWDIWikiPage $subject ) {
		// FIXME Remove wfMemcKey dep.

		return wfMemcKey( 'ext', 'semanticglossary', $subject->getSerialization() );
	}

	/**
	 * @since 1.0
	 *
	 * @return string
	 */
	public static function getCacheType() {

		if ( isset( $GLOBAL['wgexLingoCacheType'] ) && $GLOBAL['wgexLingoCacheType'] !== null ) {
			return $GLOBAL['wgexLingoCacheType'];
		}

		return $GLOBALS['wgMainCacheType'];
	}

}
