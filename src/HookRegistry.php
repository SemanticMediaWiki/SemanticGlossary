<?php

namespace SG;

use Hooks;
use MediaWiki\Linker\LinkTarget;
use SMW\DIWikiPage;

/**
 * @license GPL-2.0-or-later
 * @since 1.0
 *
 * @author mwjames
 */
class HookRegistry {

	/**
	 * @var array
	 */
	private $handlers = array();

	/**
	 * @since 1.0
	 */
	public function __construct() {
		$this->addCallbackHandlers();
	}

	/**
	 * @since  1.1
	 *
	 * @param string $name
	 *
	 * @return bool
	 */
	public function isRegistered( $name ) {
		return Hooks::isRegistered( $name );
	}

	/**
	 * @since  1.1
	 *
	 * @param string $name
	 *
	 * @return callable|false
	 */
	public function getHandlerFor( $name ) {
		return isset( $this->handlers[$name] ) ? $this->handlers[$name] : false;
	}

	/**
	 * @since  1.0
	 */
	public function register() {
		foreach ( $this->handlers as $name => $callback ) {
			Hooks::register( $name, $callback );
		}
	}

	private function addCallbackHandlers() {
		/**
		 * @see https://github.com/SemanticMediaWiki/SemanticMediaWiki/blob/master/docs/technical/hooks.md
		 */
		$this->handlers['SMW::Property::initProperties'] = static function ( $propertyRegistry ) {
			$propertyRegistrationHelper = new PropertyRegistrationHelper( $propertyRegistry );
			return $propertyRegistrationHelper->registerProperties();

		};

		/**
		 * Invalidate on update
		 *
		 * @since 1.0
		 */
		$this->handlers['SMWStore::updateDataBefore'] = static function ( $store, $semanticData ) {
			return \SG\Cache\CacheInvalidator::getInstance()->invalidateCacheOnStoreUpdate( $store, $semanticData );
		};

		/**
		 * Invalidate on delete
		 *
		 * @since 1.0
		 */
		$this->handlers['SMW::SQLStore::AfterDeleteSubjectComplete'] = static function ( $store, $title ) {
			return \SG\Cache\CacheInvalidator::getInstance()->invalidateCacheOnPageDelete(
				$store,
				DIWikiPage::newFromTitle( $title )
			);
		};

		/**
		 * Invalidate on title move
		 *
		 * @since 1.0
		 */
		if ( version_compare( MW_VERSION, "1.35.0", "<" ) ) {
			$this->handlers['TitleMoveComplete'] = static function ( &$old_title ) {
				return \SG\Cache\CacheInvalidator::getInstance()->invalidateCacheOnPageMove( $old_title );
			};
		} else {
			$this->handlers['PageMoveComplete'] = static function ( LinkTarget $old_title ) {
				return \SG\Cache\CacheInvalidator::getInstance()->invalidateCacheOnPageMove( $old_title );
			};
		}
	}

}
