<?php

namespace SG;

use SMW\Store;
use SMW\DIWikiPage;
use Hooks;

/**
 * @license GNU GPL v2+
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
	 *
	 * @param Store $store
	 */
	public function __construct( Store $store ) {
		$this->addCallbackHandlers( $store );
	}

	/**
	 * @since  1.1
	 *
	 * @param string $name
	 *
	 * @return boolean
	 */
	public function isRegistered( $name ) {
		return Hooks::isRegistered( $name );
	}

	/**
	 * @since  1.1
	 *
	 * @param string $name
	 *
	 * @return Callable|false
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

	private function addCallbackHandlers( $store ) {

		$propertyRegistry = new PropertyRegistry();

		/**
		 * @see https://github.com/SemanticMediaWiki/SemanticMediaWiki/blob/master/docs/technical/hooks.md
		 */
		$this->handlers['SMW::Property::initProperties'] = function () use ( $propertyRegistry ) {
			return PropertyRegistry::getInstance()->register();
		};

		/**
		 * Invalidate on update
		 *
		 * @since 1.0
		 */
		$this->handlers['SMWStore::updateDataBefore'] = function ( $store, $semanticData ) {
			return \SG\Cache\CacheInvalidator::getInstance()->invalidateCacheOnStoreUpdate( $store, $semanticData );
		};

		/**
		 * Invalidate on delete
		 *
		 * @since 1.0
		 */
		$this->handlers['SMW::SQLStore::AfterDeleteSubjectComplete'] = function ( $store, $title ) {
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
		$this->handlers['TitleMoveComplete'] = function ( &$old_title ) {
			return \SG\Cache\CacheInvalidator::getInstance()->invalidateCacheOnPageMove( $old_title );
		};

	}

}
