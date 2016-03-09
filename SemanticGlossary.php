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

		// must NOT use ExtensionRegistry::getInstance() to avoid recursion!
		$registry = new ExtensionRegistry();
		$registry->load( $GLOBALS[ 'wgExtensionDirectory' ] . '/Lingo/extension.json' );

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
