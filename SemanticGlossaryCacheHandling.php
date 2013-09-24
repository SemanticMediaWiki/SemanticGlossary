<?php

/**
 * File holding the SemanticGlossaryCacheHandling class
 *
 * @author Stephan Gambke
 * @file
 * @ingroup SemanticGlossary
 */
if ( !defined( 'SG_VERSION' ) ) {
	die( 'This file is part of the SemanticGlossary extension, it is not a valid entry point.' );
}

/**
 * The SemanticGlossaryCacheHandling class.
 *
 * @ingroup SemanticGlossary
 */
class SemanticGlossaryCacheHandling {

	/**
	 * Initiates the purging of the cache when a Glossary property was changed.
	 *
	 * @param Page $wikipage
	 * @return Bool
	 */
	static function purgeCacheForData( SMWStore $store, SMWSemanticData $data ) {

		wfProfileIn( __METHOD__ );

		// get properties
		$properties = $data->getProperties();

		$subject = $data->getSubject();

		// first handle subobjects recursively
		if ( array_key_exists( '_SOBJ', $properties ) ) {
			foreach ( $data->getPropertyValues( $properties['_SOBJ'] ) as $so ) {
				self::purgeCacheForData( $store, $store->getSemanticData($so), false );
			}
		}

		// check if terms, definitions or links changed
		if ( self::propValuesChanged( $store, $data, $subject, $properties, '___glt' ) ||
			self::propValuesChanged( $store, $data, $subject, $properties, '___gld' ) ||
			self::propValuesChanged( $store, $data, $subject, $properties, '___gll' ) ||
			self::propValuesChanged( $store, $data, $subject, $properties, '___gls' )
		) {
			self::purgeSubjectFromCache( $subject );
			LingoParser::purgeCache();
		}

		wfProfileOut( __METHOD__ );
		return true;
	}

	/**
	 * Initiates the purging of the cache for a given subject page.
	 *
	 * @param Page $wikipage
	 * @return Bool
	 */
	static function purgeCacheForSubject( SMWDIWikiPage $subject, $purgeLingo = true ) {

		wfProfileIn( __METHOD__ );

		// get the store
		$store = smwfGetStore();

		// get its properties
		$properties = $store->getProperties($subject);

		// first handle subobjects recursively
		if ( array_key_exists( '_SOBJ', $properties ) ) {
			foreach ( $store->getPropertyValues( $subject, $properties['_SOBJ'] ) as $so ) {
				self::purgeCacheForSubject( $so->getSubject(), false );
			}
		}

		self::purgeSubjectFromCache( $subject );
		if ( $purgeLingo ) {
			LingoParser::purgeCache();
		}

		wfProfileOut( __METHOD__ );
		return true;
	}

	/**
	 * Initiates the purging of the cache for a given title.
	 * Handler for TitleMoveComplete hook.
	 *
	 * @param Page $wikipage
	 * @return Bool
	 */
	static public function purgeCacheForTitle( &$old_title, &$new_title, &$user, $pageid, $redirid ) {
		self::purgeCacheForSubject( SMWDIWikiPage::newFromTitle( $old_title ) );
		return true;
	}

	/**
	 * Check if the values of the given property changed.
	 * To be unchanged every old value must match against exactly one new.
	 *
	 * @param SMWStore $store
	 * @param SMWSemanticData $data provides the subject and by that the new data
	 * @param type $properties contains the old data
	 * @param type $propId the id of the property to be checked
	 * @return boolean
	 */
	static protected function propValuesChanged( SMWStore &$store, SMWSemanticData &$data, SMWDIWikiPage &$subject, &$properties, $propId ) {

		// check if property changed
		if ( array_key_exists( $propId, $properties ) ) {
			$newEntries = $data->getPropertyValues( $properties[$propId] );
			$oldEntries = $store->getPropertyValues( $subject, $properties[$propId] );
		} else {
			$newEntries = array();
			$oldEntries = $store->getPropertyValues( $subject, new SMWDIProperty( $propId ) );
		}

		// Did the number of entries change?
		if ( count( $newEntries ) !== count( $oldEntries ) ) {
			return true;
		}

		// Match each new entry against an old entry
		foreach ( $newEntries as $newDi ) {
			$found = false;
			foreach ( $oldEntries as $oldKey => $oldDi ) {
				if ( $newDi->getHash() === $oldDi->getHash() ) {
					$found = true;
					unset( $oldEntries[$oldKey] );
					break;
				}
			}

			// If no match was possible...
			if ( !$found ) {
				return true;
			}
		}

		// Are there unmatched old entries left?
		if ( count( $oldEntries ) > 0 ) {
			return true;
		}

		// Every new entry matched to exaclty one old entry and vice versa
		return false;
	}

	/**
	 * Purges the glossary entry for the given SMWSemanticData object from the cache.
	 */
	static protected function purgeSubjectFromCache( SMWDIWikiPage &$subject ) {

		wfProfileIn( __METHOD__ );

		global $wgexLingoCacheType;
		$cache = ($wgexLingoCacheType !== null) ? wfGetCache( $wgexLingoCacheType ) : wfGetMainCache();
		$cachekey = wfMemcKey( 'ext', 'semanticglossary', $subject->getSerialization() );
		$cache->delete( $cachekey );

		wfProfileOut( __METHOD__ );
		return true;
	}

}
