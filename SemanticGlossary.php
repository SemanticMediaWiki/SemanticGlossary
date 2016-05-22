<?php

use SG\HookRegistry;
use SMW\ApplicationFactory;

/**
 * Class SemanticGlossary
 *
 * @ingroup Skins
 */
class SemanticGlossary {

	/**
	 * @since 2.0
	 */
	public static function initExtension() {

		if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
			require_once __DIR__ . '/vendor/autoload.php';
		}

		// must NOT use ExtensionRegistry::getInstance() to avoid recursion!
		$registry = new ExtensionRegistry();
		if ( file_exists( __DIR__ . '/extensions/Lingo/extension.json' ) ) {
			$registry->load( __DIR__ . '/extensions/Lingo/extension.json' );
		} else {
			$registry->load( $GLOBALS[ 'wgExtensionDirectory' ] . '/Lingo/extension.json' );
		}

		$GLOBALS[ 'wgexLingoBackend' ] = 'SG\LingoBackendAdapter';

		$GLOBALS[ 'wgExtensionFunctions' ][] = function () {

			$hookRegistry = new HookRegistry(
				ApplicationFactory::getInstance()->getStore()
			);

			$hookRegistry->register();
		};
	}

	/**
	 * @since 2.0
	 *
	 * @return string|null
	 */
	public static function getVersion() {
		$extensionData = ExtensionRegistry::getInstance()->getAllThings();

		if ( isset( $extensionData['Semantic Glossary'] ) ) {
			return $extensionData['Semantic Glossary']['version'];
		}

		return null;
	}
}
