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

/**
 * The Semantic Glossary version
 */
define( 'SG_VERSION', '0.1 alpha' );

// register the extension
$wgExtensionCredits[defined( 'SEMANTIC_EXTENSION_TYPE' ) ? 'semantic' : 'other'][] = array(
	'path' => __FILE__,
	'name' => 'Semantic Glossary',
	'author' => '[http://www.mediawiki.org/wiki/User:F.trott|Stephan Gambke]',
	'url' => 'http://www.mediawiki.org/wiki/Extension:Semantic_Glossary',
	'descriptionmsg' => 'semanticglossary-desc',
	'version' => SG_VERSION,
);

// server-local path to this file
$dir = dirname( __FILE__ );

// register message file
$wgExtensionMessagesFiles['SemanticGlossary'] = $dir . '/SemanticGlossary.i18n.php';
$wgExtensionMessagesFiles['SemanticGlossaryAlias'] = $dir . '/SemanticGlossary.alias.php';

// register class files with the Autoloader
$wgAutoloadClasses['SemanticGlossarySettings'] = $dir . '/SemanticGlossarySettings.php';
$wgAutoloadClasses['SemanticGlossaryParser'] = $dir . '/SemanticGlossaryParser.php';
$wgAutoloadClasses['SemanticGlossaryTree'] = $dir . '/SemanticGlossaryTree.php';
$wgAutoloadClasses['SemanticGlossaryElement'] = $dir . '/SemanticGlossaryElement.php';
$wgAutoloadClasses['SemanticGlossaryBackend'] = $dir . '/SemanticGlossaryBackend.php';
$wgAutoloadClasses['SemanticGlossaryMessageLog'] = $dir . '/SemanticGlossaryMessageLog.php';
$wgAutoloadClasses['SpecialSemanticGlossaryBrowser'] = $dir . '/SpecialSemanticGlossaryBrowser.php';

// register Special pages
$wgSpecialPages['SemanticGlossaryBrowser'] = 'SpecialSemanticGlossaryBrowser';
$wgSpecialPageGroups['SemanticGlossaryBrowser'] = 'other';

// register hook handlers
// $wgHooks['ParserFirstCallInit'][] = 'SemanticGlossarySetup';  // Define a setup function
$wgHooks['ParserAfterTidy'][] = 'SemanticGlossaryParser::parse';

$wgHooks['smwInitProperties'][] = 'SemanticGlossaryRegisterProperties';
$wgHooks['smwInitDatatypes'][] = 'SemanticGlossaryRegisterPropertyAliases';

// register resource modules with the Resource Loader
$wgResourceModules['ext.SemanticGlossary'] = array(
	// JavaScript and CSS styles. To combine multiple file, just list them as an array.
	// 'scripts' => 'js/ext.myExtension.js',
	'styles' => 'css/SemanticGlossary.css',

	// When your module is loaded, these messages will be available to mediaWiki.msg()
	// 'messages' => array( 'myextension-hello-world', 'myextension-goodbye-world' ),

	// If your scripts need code from other modules, list their identifiers as dependencies
	// and ResourceLoader will make sure they're loaded before you.
	// You don't need to manually list 'mediawiki' or 'jquery', which are always loaded.
	// 'dependencies' => array( 'jquery.ui.datepicker' ),

	// ResourceLoader needs to know where your files are; specify your
	// subdir relative to "extensions" or $wgExtensionAssetsPath
	'localBasePath' => dirname( __FILE__ ),
	'remoteExtPath' => 'SemanticGlossary'
);

$wgResourceModules['ext.SemanticGlossary.Browser'] = array(
	'styles' => 'css/SemanticGlossaryBrowser.css',
	'localBasePath' => dirname( __FILE__ ),
	'remoteExtPath' => 'SemanticGlossary'
);

// Create new permission 'editglossary' and assign it to usergroup 'user' by default
$wgGroupPermissions['user']['editglossary'] = true;

/**
 * Handler for late setup of Semantic Glossary
 */
// function SemanticGlossarySetup () {
//
//	return true;
// }

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
