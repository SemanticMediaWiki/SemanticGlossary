<?php

namespace SG;

use MediaWiki\Linker\LinkTarget;
use SG\Cache\CacheInvalidator;
use SMW\DataItems\WikiPage;

/**
 * Static hook handlers, registered declaratively through extension.json.
 *
 * @see https://github.com/SemanticMediaWiki/SemanticMediaWiki/blob/master/docs/technical/hooks.md
 *
 * @license GPL-2.0-or-later
 * @since 1.0
 *
 * @author mwjames
 */
class HookRegistry {

	/**
	 * Register the Semantic Glossary properties.
	 *
	 * @since 1.0
	 *
	 * @param mixed $propertyRegistry
	 *
	 * @return bool
	 */
	public static function onInitProperties( $propertyRegistry ) {
		$propertyRegistrationHelper = new PropertyRegistrationHelper( $propertyRegistry );
		return $propertyRegistrationHelper->registerProperties();
	}

	/**
	 * Invalidate the glossary cache on store update.
	 *
	 * @since 1.0
	 *
	 * @param mixed $store
	 * @param mixed $semanticData
	 *
	 * @return bool
	 */
	public static function onBeforeDataUpdateComplete( $store, $semanticData ) {
		return CacheInvalidator::getInstance()->invalidateCacheOnStoreUpdate( $store, $semanticData );
	}

	/**
	 * Invalidate the glossary cache on subject delete.
	 *
	 * @since 1.0
	 *
	 * @param mixed $store
	 * @param mixed $title
	 *
	 * @return bool
	 */
	public static function onAfterDeleteSubjectComplete( $store, $title ) {
		return CacheInvalidator::getInstance()->invalidateCacheOnPageDelete(
			$store,
			WikiPage::newFromTitle( $title )
		);
	}

	/**
	 * Invalidate the glossary cache on title move.
	 *
	 * @since 1.0
	 *
	 * @param LinkTarget $old_title
	 *
	 * @return bool
	 */
	public static function onPageMoveComplete( LinkTarget $old_title ) {
		return CacheInvalidator::getInstance()->invalidateCacheOnPageMove( $old_title );
	}

}
