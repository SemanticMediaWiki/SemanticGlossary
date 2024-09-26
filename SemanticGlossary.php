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
		$GLOBALS[ 'wgexLingoBackend' ] = 'SG\LingoBackendAdapter';
	}

	/**
	 * @since 4.1.0
	 */
	public static function onExtensionFunction(): void {
		$hookRegistry = new HookRegistry();
		$hookRegistry->register();
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
