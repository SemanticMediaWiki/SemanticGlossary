<?php

namespace SG\Cache;

use Lingo\LingoParser;
use MediaWiki\Linker\LinkTarget;
use SG\PropertyRegistrationHelper;
use SG\SemanticDataComparator;
use SMW\DIProperty;
use SMW\DIWikiPage;
use SMW\SemanticData;
use SMW\Store;

/**
 * @ingroup SG
 * @ingroup SemanticGlossary
 *
 * @license GPL-2.0-or-later
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
	 * @param SemanticData|null $semanticData
	 *
	 * @return bool
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
	 * @param bool|true $purgeLingo
	 *
	 * @return bool
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
	 * @param LinkTarget $title
	 *
	 * @return bool
	 */
	public function invalidateCacheOnPageMove( LinkTarget $title ) {
		$this->purgeCache( DIWikiPage::newFromText( $title->getDBkey(), $title->getNamespace() ) );
		return true;
	}

	/**
	 * @param Store $store
	 * @param SemanticData $semanticData
	 *
	 * @return void
	 */
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

	/**
	 * @param Store $store
	 * @param DIWikiPage $subject
	 *
	 * @return void
	 */
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

	/**
	 * @param Store $store
	 * @param SemanticData $semanticData
	 *
	 * @return bool
	 */
	private function hasSemanticDataDeviation( Store $store, SemanticData $semanticData ) {
		$dataComparator = new SemanticDataComparator( $store, $semanticData );

		return $dataComparator->compareForProperty( PropertyRegistrationHelper::SG_TERM ) ||
			$dataComparator->compareForProperty( PropertyRegistrationHelper::SG_DEFINITION ) ||
			$dataComparator->compareForProperty( PropertyRegistrationHelper::SG_LINK ) ||
			$dataComparator->compareForProperty( PropertyRegistrationHelper::SG_STYLE );
	}

	/**
	 * @param DIWikiPage $subject
	 *
	 * @return true
	 */
	private function purgeCache( DIWikiPage $subject ) {
		$this->glossaryCache->getCache()->delete(
			$this->glossaryCache->getKeyForSubject( $subject )
		);

		return true;
	}

}
