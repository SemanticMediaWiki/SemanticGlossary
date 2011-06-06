<?php

/**
 * A terminology markup extension with a Semantic MediaWiki backend
 *
 * @defgroup SemanticGlossary Semantic Glossary
 * @author Stephan Gambke
 * @version 0.1 alpha
 */

/**
 * The main file of the SemanticGlossary extension
 *
 * @author Stephan Gambke
 *
 * @file
 * @ingroup SemanticGlossary
 */


if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is part of a MediaWiki extension, it is not a valid entry point.' );
}

if ( !defined( 'SMW_VERSION' ) ) {
	die( 'Semantic Glossary depends on the Semantic MediaWiki extension. You need to install Semantic MediaWiki first.' );
}

if ( !defined( 'LINGO_VERSION' ) ) {
	die( 'Semantic Glossary depends on the Lingo extension. You need to install Lingo first.' );
}

/**
 * The Semantic Glossary version
 */
define( 'SG_VERSION', '0.1 alpha' );

// register the extension
$wgExtensionCredits[defined( 'SEMANTIC_EXTENSION_TYPE' ) ? 'semantic' : 'other'][] = array(
	'path' => __FILE__,
	'name' => 'Semantic Glossary',
	'author' => '[http://www.mediawiki.org/wiki/User:F.trott Stephan Gambke]',
	'url' => 'http://www.mediawiki.org/wiki/Extension:Semantic_Glossary',
	'descriptionmsg' => 'semanticglossary-desc',
	'version' => SG_VERSION,
);


// set SemanticGlossaryBackend as the backend to access the glossary
$wgexLingoBackend = 'SemanticGlossaryBackend';

// server-local path to this file
$dir = dirname( __FILE__ );

// register message file
$wgExtensionMessagesFiles['SemanticGlossary'] = $dir . '/SemanticGlossary.i18n.php';
$wgExtensionMessagesFiles['SemanticGlossaryAlias'] = $dir . '/SemanticGlossary.alias.php';

// register class files with the Autoloader
$wgAutoloadClasses['SemanticGlossaryBackend'] = $dir . '/SemanticGlossaryBackend.php';
$wgAutoloadClasses['SpecialSemanticGlossaryBrowser'] = $dir . '/SpecialSemanticGlossaryBrowser.php';

// register Special pages
$wgSpecialPages['SemanticGlossaryBrowser'] = 'SpecialSemanticGlossaryBrowser';
$wgSpecialPageGroups['SemanticGlossaryBrowser'] = 'other';

// register hook handlers
$wgHooks['smwInitProperties'][] = 'SemanticGlossaryRegisterProperties';
$wgHooks['smwInitDatatypes'][] = 'SemanticGlossaryRegisterPropertyAliases';

// register resource modules with the Resource Loader
$wgResourceModules['ext.SemanticGlossary.Browser'] = array(
	'localBasePath' => $dir,
	'styles' => 'css/SemanticGlossaryBrowser.css',
	'remoteExtPath' => 'SemanticGlossary'
);

// Create new permission 'editglossary' and assign it to usergroup 'user' by default
$wgGroupPermissions['user']['editglossary'] = true;

define( 'SG_PROP_GLT', 'Glossary-Term' );
define( 'SG_PROP_GLD', 'Glossary-Definition' );
define( 'SG_PROP_GLL', 'Glossary-Link' );

function SemanticGlossaryRegisterProperties() {
	SMWDIProperty::registerProperty( '___glt', '_str', SG_PROP_GLT, true );
	SMWDIProperty::registerProperty( '___gld', '_txt', SG_PROP_GLD, true );
	SMWDIProperty::registerProperty( '___gll', '_str', SG_PROP_GLL, true );
	return true;
}

function SemanticGlossaryRegisterPropertyAliases() {
	SMWDIProperty::registerPropertyAlias( '___glt', wfMsg( 'semanticglossary-prop-glt' ) );
	SMWDIProperty::registerPropertyAlias( '___gld', wfMsg( 'semanticglossary-prop-gld' ) );
	SMWDIProperty::registerPropertyAlias( '___gll', wfMsg( 'semanticglossary-prop-gll' ) );
	return true;
}
