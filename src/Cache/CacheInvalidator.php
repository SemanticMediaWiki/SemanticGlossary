<?php

namespace SG\Cache;

use SG\SemanticDataComparator;
use SG\PropertyRegistrationHelper;

use SMW\Store;
use SMW\SemanticData;
use SMW\DIWikiPage;
use SMW\DIProperty;

use Lingo\LingoParser;

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

	/**
	 * @var CacheInvalidator
	 */
	private static $instance = null;

	/**
	 * @var GlossaryCache
	 */
	private $cache = null;

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
	public function invalidateCacheOnStoreUpdate( Store $store, SemanticData $semanticData = null ) {

		if ( $semanticData === null ) {
			return false;
		}

		$this->matchAllSubobjects( $store, $semanticData );

		if ( $this->hasSemanticDataDeviation( $store, $semanticData ) ) {
			$this->purgeCache( $semanticData->getSubject() );
			LingoParser::purgeCache();
		}

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

		$this->matchSubobjectsToSubject( $store, $subject );
		$this->purgeCache( $subject );

		if ( $purgeLingo ) {
			LingoParser::purgeCache();
		}

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

	private function matchAllSubobjects( Store $store, SemanticData $semanticData ) {

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

	private function matchSubobjectsToSubject( Store $store, DIWikiPage $subject ) {

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

	private function hasSemanticDataDeviation( Store $store, SemanticData $semanticData ) {

		$dataComparator = new SemanticDataComparator( $store, $semanticData );

		return $dataComparator->compareForProperty( PropertyRegistrationHelper::SG_TERM ) ||
			$dataComparator->compareForProperty( PropertyRegistrationHelper::SG_DEFINITION ) ||
			$dataComparator->compareForProperty( PropertyRegistrationHelper::SG_LINK ) ||
			$dataComparator->compareForProperty( PropertyRegistrationHelper::SG_STYLE );
	}

	private function purgeCache( DIWikiPage $subject ) {

		$this->glossaryCache->getCache()->delete(
			$this->glossaryCache->getKeyForSubject( $subject )
		);

		return true;
	}

}
