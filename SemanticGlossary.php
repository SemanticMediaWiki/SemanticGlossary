<?php

/**
 * A terminology markup extension with a Semantic MediaWiki backend
 *
 * @defgroup SemanticGlossary Semantic Glossary
 * @author Stephan Gambke
 * @version 1.0.0
 */

/**
 * The main file of the SemanticGlossary extension
 *
 * @author Stephan Gambke
 *
 * @file
 * @ingroup SemanticGlossary
 */

call_user_func( function () {

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
	define( 'SG_VERSION', '1.0.0' );

	// register the extension
	$GLOBALS[ 'wgExtensionCredits' ][ 'semantic' ][] = array(
		'path' => __FILE__,
		'name' => 'Semantic Glossary',
		'author' => '[http://www.mediawiki.org/wiki/User:F.trott Stephan Gambke]',
		'url' => 'https://www.mediawiki.org/wiki/Extension:Semantic_Glossary',
		'descriptionmsg' => 'semanticglossary-desc',
		'version' => SG_VERSION,
	);


	// set SemanticGlossaryBackend as the backend to access the glossary
	$GLOBALS[ 'wgexLingoBackend' ] = 'SemanticGlossaryBackend';

	// server-local path to this file
	$dir = dirname( __FILE__ );

	// register message file
	$GLOBALS[ 'wgExtensionMessagesFiles' ]['SemanticGlossary'] = $dir . '/SemanticGlossary.i18n.php';

	// register class files with the Autoloader
	$autoloadClasses = array(
		'SemanticGlossaryBackend' => $dir . '/SemanticGlossaryBackend.php',
		'SemanticGlossaryCacheHandling' => $dir . '/SemanticGlossaryCacheHandling.php',
	);

	$GLOBALS[ 'wgAutoloadClasses' ] = array_merge( $GLOBALS[ 'wgAutoloadClasses' ], $autoloadClasses );

	// register hook handlers
	$hooks = array(
		'smwInitProperties' => array( 'SemanticGlossaryBackend::registerProperties' ),
		'smwInitDatatypes' => array( 'SemanticGlossaryBackend::registerPropertyAliases' ),

		'SMWStore::updateDataBefore' => array( 'SemanticGlossaryCacheHandling::purgeCacheForData' ), // invalidate on update
		'smwDeleteSemanticData' => array( 'SemanticGlossaryCacheHandling::purgeCacheForSubject' ), // invalidate on delete
		'TitleMoveComplete' => array( 'SemanticGlossaryCacheHandling::purgeCacheForTitle' ), // move annotations
	);

	$GLOBALS[ 'wgHooks' ] = array_merge_recursive( $GLOBALS[ 'wgHooks' ], $hooks );

	define( 'SG_PROP_GLT', 'Glossary-Term' );
	define( 'SG_PROP_GLD', 'Glossary-Definition' );
	define( 'SG_PROP_GLL', 'Glossary-Link' );
	define( 'SG_PROP_GLS', 'Glossary-Style' );

} );
