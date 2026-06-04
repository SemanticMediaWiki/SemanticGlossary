<?php

namespace SG\Cache;

use Lingo\LingoParser;
use MediaWiki\Linker\LinkTarget;
use SG\PropertyRegistrationHelper;
use SG\SemanticDataComparator;
use SMW\DataItems\Property;
use SMW\DataItems\WikiPage;
use SMW\DataModel\SemanticData;
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
	private $glossaryCache = null;

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
	public function invalidateCacheOnStoreUpdate( Store $store, ?SemanticData $semanticData = null ) {
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
	 * @param WikiPage $subject
	 * @param bool|true $purgeLingo
	 *
	 * @return bool
	 */
	public function invalidateCacheOnPageDelete( Store $store, WikiPage $subject, $purgeLingo = true ) {
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
		$this->purgeCache( WikiPage::newFromText( $title->getDBkey(), $title->getNamespace() ) );
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

		if ( array_key_exists( Property::TYPE_SUBOBJECT, $properties ) ) {
			foreach ( $semanticData->getPropertyValues( $properties[ Property::TYPE_SUBOBJECT ] ) as $subobject ) {
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
	 * @param WikiPage $subject
	 *
	 * @return void
	 */
	private function matchSubobjectsToSubject( Store $store, WikiPage $subject ) {
		$properties = $store->getProperties( $subject );

		if ( array_key_exists( Property::TYPE_SUBOBJECT, $properties ) ) {
			foreach ( $store->getPropertyValues( $subject, $properties[ Property::TYPE_SUBOBJECT ] ) as $subobject ) {
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
	 * @param WikiPage $subject
	 *
	 * @return true
	 */
	private function purgeCache( WikiPage $subject ) {
		$this->glossaryCache->getCache()->delete(
			$this->glossaryCache->getKeyForSubject( $subject )
		);

		return true;
	}

}
