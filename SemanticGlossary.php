<?php

use SG\HookRegistry;
use SMW\ApplicationFactory;

/**
 * A terminology markup extension with a Semantic MediaWiki backend
 *
 * @defgroup SemanticGlossary Semantic Glossary
 * @author Stephan Gambke
 */
if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is part of a MediaWiki extension, it is not a valid entry point.' );
}

if ( defined( 'SG_VERSION' ) ) {
	// Do not initialize more than once.
	return 1;
}

/**
 * The Semantic Glossary version
 */
define( 'SG_VERSION', '1.2.0-alpha' );

if ( !defined( 'SMW_VERSION' ) ) {
	die( 'Semantic Glossary depends on the Semantic MediaWiki extension. You need to install Semantic MediaWiki first.' );
}

if ( !defined( 'LINGO_VERSION' ) ) {
	die( 'Semantic Glossary depends on the Lingo extension. You need to install Lingo first.' );
}

call_user_func( function () {

	// register the extension
	$GLOBALS[ 'wgExtensionCredits' ][ 'semantic' ][] = array(
		'path' => __FILE__,
		'name' => 'Semantic Glossary',
		'author' => array( '[http://www.mediawiki.org/wiki/User:F.trott Stephan Gambke]', 'James Hong Kong' ),
		'url' => 'https://www.mediawiki.org/wiki/Extension:Semantic_Glossary',
		'descriptionmsg' => 'semanticglossary-desc',
		'version' => SG_VERSION,
		'license-name' => 'GPL-2.0+'
	);

	// set SemanticGlossaryBackend as the backend to access the glossary
	$GLOBALS[ 'wgexLingoBackend' ] = 'SG\LingoBackendAdapter';

	// server-local path to this file
	$dir = __DIR__;

	// register message file
	$GLOBALS[ 'wgMessagesDirs' ]['SemanticGlossary'] = $dir . '/i18n';
	$GLOBALS[ 'wgExtensionMessagesFiles' ]['SemanticGlossary'] = $dir . '/SemanticGlossary.i18n.php';

	$GLOBALS['wgExtensionFunctions'][] = function() {

		$hookRegistry = new HookRegistry(
			ApplicationFactory::getInstance()->getStore()
		);

		$hookRegistry->register();
	};

} );
