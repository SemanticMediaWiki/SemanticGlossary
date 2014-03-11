<?php

namespace SG;

use LingoParser;
use BagOStuff;
use Title;

/**
 * @ingroup SG
 *
 * @licence GNU GPL v2+
 * @since 1.0
 *
 * @author Stephan Gambke
 * @author mwjames
 */
class CacheInvalidator {

	protected static $instance = null;
	protected $cache = null;

	/**
	 * @since 1.0
	 *
	 * @return CacheInvalidator
	 */
	public static function getInstance() {

		if ( self::$instance === null ) {

			$instance = new self();
			$instance->setCache( CacheHelper::getCache() );

			self::$instance = $instance;
		}

		return self::$instance;
	}

	/**
	 * @since 1.0
	 */
	public static function clear() {
		self::$instance = null;
	}

	/**
	 * @since 1.0
	 *
	 * @param BagOStuff $cache
	 */
	public function setCache( BagOStuff $cache ) {
		$this->cache = $cache;
	}

	/**
	 * @since 1.0
	 *
	 * @param Store $store
	 * @param SemanticData $semanticData
	 *
	 * @return boolean
	 */
	public function invalidateCacheOnStoreUpdate( \SMWStore $store, \SMWSemanticData $semanticData ) {

		wfProfileIn( __METHOD__ );

		$this->matchAllSubobjects( $store, $semanticData );

		if ( $this->hasSemanticDataDeviation( $store, $semanticData ) ) {
			$this->purgeCache( $semanticData->getSubject() );
			LingoParser::purgeCache();
		}

		wfProfileOut( __METHOD__ );
		return true;
	}

	/**
	 * @since 1.0
	 *
	 * @param SMWStore $store
	 * @param SMWDIWikiPage $subject
	 * @param boolean|true $purgeLingo
	 *
	 * @return boolean
	 */
	public function invalidateCacheOnPageDelete( \SMWStore $store, \SMWDIWikiPage $subject, $purgeLingo = true ) {

		wfProfileIn( __METHOD__ );

		$this->matchSubobjectsToSubject( $store, $subject );
		$this->purgeCache( $subject );

		if ( $purgeLingo ) {
			LingoParser::purgeCache();
		}

		wfProfileOut( __METHOD__ );
		return true;
	}

	/**
	 * @since 1.0
	 *
	 * @param Title $title
	 *
	 * @return boolean
	 */
	public function invalidateCacheOnPageMove( Title $title ) {
		$this->purgeCache( \SMWDIWikiPage::newFromTitle( $title ) );
		return true;
	}

	protected function matchAllSubobjects( \SMWStore $store, \SMWSemanticData $semanticData ) {

		$properties = $semanticData->getProperties();

		if ( array_key_exists( '_SOBJ', $properties ) ) {
			foreach ( $semanticData->getPropertyValues( $properties['_SOBJ'] ) as $subobject ) {
				$this->invalidateCacheOnStoreUpdate(
					$store,
					$semanticData->findSubSemanticData( $subobject->getSubobjectName() ),
					false
				);
			}
		}
	}

	protected function matchSubobjectsToSubject( \SMWStore $store, \SMWDIWikiPage $subject ) {

		$properties = $store->getProperties( $subject );

		if ( array_key_exists( '_SOBJ', $properties ) ) {
			foreach ( $store->getPropertyValues( $subject, $properties['_SOBJ'] ) as $subobject ) {
				$this->invalidateCacheOnPageDelete(
					$store,
					$subobject->getSubject(),
					false
				);
			}
		}
	}

	protected function hasSemanticDataDeviation( \SMWStore $store, \SMWSemanticData $semanticData ) {

		$dataComparator = new DataComparator( $store, $semanticData );

		return $dataComparator->byPropertyId( PropertyRegistry::SG_TERM ) ||
			$dataComparator->byPropertyId( PropertyRegistry::SG_DEFINITION ) ||
			$dataComparator->byPropertyId( PropertyRegistry::SG_LINK ) ||
			$dataComparator->byPropertyId( PropertyRegistry::SG_STYLE );
	}

	protected function purgeCache( \SMWDIWikiPage $subject ) {
		wfProfileIn( __METHOD__ );

		$this->cache->delete( CacheHelper::getKey( $subject ) );

		wfProfileOut( __METHOD__ );
		return true;
	}

}
