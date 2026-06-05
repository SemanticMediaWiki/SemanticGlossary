<?php

namespace SG;

/**
 * @license GPL-2.0-or-later
 * @since 7.0
 *
 * @author mwjames
 */
class Setup {

	/**
	 * Register Semantic Glossary as the Lingo backend.
	 *
	 * The $wgexLingoBackend configuration global is owned by the Lingo
	 * extension (which defaults it to Lingo\BasicBackend), so Semantic
	 * Glossary cannot declare it in extension.json `config` — MediaWiki
	 * refuses to let two extensions set the same config global. It is set
	 * here instead, leaving an explicit override in LocalSettings.php
	 * untouched.
	 *
	 * @since 7.0
	 */
	public static function onExtensionFunction(): void {
		// The literal mirrors Lingo's own declared default (extension.json
		// config_prefix "wgex" + key "LingoBackend"). Anything else means the
		// user picked a backend in LocalSettings.php, which we leave untouched.
		// Keep this in sync if Lingo ever changes its default backend.
		if (
			!isset( $GLOBALS['wgexLingoBackend'] )
			|| $GLOBALS['wgexLingoBackend'] === 'Lingo\\BasicBackend'
		) {
			$GLOBALS['wgexLingoBackend'] = LingoBackendAdapter::class;
		}
	}

}
