<?php

namespace SG\Cache;

use SG\SemanticDataComparator;
use SG\PropertyRegistry;

use SMW\Store;
use SMW\SemanticData;
use SMW\DIWikiPage;
use SMW\DIProperty;

use LingoParser;

use Title;

/**
 * @ingroup SG
 * @ingroup SemanticGlossary
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author Stephan Gambke
 * @author mwjames
 */
class CacheInvalidator {

	protected static $instance = null;

	/* @var GlossaryCache */
	protected $cache = null;

	/**
	 * @since 1.0
	 *
	 * @return CacheInvalidator
	 */
	public static function getInstance() {

		if ( self::$instance === null ) {

			$instance = new self();
			$instance->setCache( new GlossaryCache() );

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
	 * @param GlossaryCache $glossaryCache
	 */
	public function setCache( GlossaryCache $glossaryCache ) {
		$this->glossaryCache = $glossaryCache;
	}

	/**
	 * @since 1.0
	 *
	 * @param Store $store
	 * @param SemanticData $semanticData
	 *
	 * @return boolean
	 */
	public function invalidateCacheOnStoreUpdate( Store $store, SemanticData $semanticData ) {

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
	 * @param Store $store
	 * @param DIWikiPage $subject
	 * @param boolean|true $purgeLingo
	 *
	 * @return boolean
	 */
	public function invalidateCacheOnPageDelete( Store $store, DIWikiPage $subject, $purgeLingo = true ) {

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
		$this->purgeCache( DIWikiPage::newFromTitle( $title ) );
		return true;
	}

	protected function matchAllSubobjects( Store $store, SemanticData $semanticData ) {

		$properties = $semanticData->getProperties();

		if ( array_key_exists( DIProperty::TYPE_SUBOBJECT, $properties ) ) {
			foreach ( $semanticData->getPropertyValues( $properties[ DIProperty::TYPE_SUBOBJECT ] ) as $subobject ) {
				$this->invalidateCacheOnStoreUpdate(
					$store,
					$semanticData->findSubSemanticData( $subobject->getSubobjectName() ),
					false
				);
			}
		}
	}

	protected function matchSubobjectsToSubject( Store $store, DIWikiPage $subject ) {

		$properties = $store->getProperties( $subject );

		if ( array_key_exists( DIProperty::TYPE_SUBOBJECT, $properties ) ) {
			foreach ( $store->getPropertyValues( $subject, $properties[ DIProperty::TYPE_SUBOBJECT ] ) as $subobject ) {
				$this->invalidateCacheOnPageDelete(
					$store,
					$subobject->getSubject(),
					false
				);
			}
		}
	}

	protected function hasSemanticDataDeviation( Store $store, SemanticData $semanticData ) {

		$dataComparator = new SemanticDataComparator( $store, $semanticData );

		return $dataComparator->byPropertyId( PropertyRegistry::SG_TERM ) ||
			$dataComparator->byPropertyId( PropertyRegistry::SG_DEFINITION ) ||
			$dataComparator->byPropertyId( PropertyRegistry::SG_LINK ) ||
			$dataComparator->byPropertyId( PropertyRegistry::SG_STYLE );
	}

	protected function purgeCache( DIWikiPage $subject ) {
		wfProfileIn( __METHOD__ );

		$this->glossaryCache->getCache()->delete(
			$this->glossaryCache->getKeyForSubject( $subject )
		);

		wfProfileOut( __METHOD__ );
		return true;
	}

}
