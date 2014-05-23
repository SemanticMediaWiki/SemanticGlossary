<?php
/**
 * A terminology markup extension with a Semantic MediaWiki backend
 *
 * @defgroup SemanticGlossary Semantic Glossary
 * @author Stephan Gambke
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
	define( 'SG_VERSION', '1.1.0-dev' );

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
	$GLOBALS[ 'wgexLingoBackend' ] = 'SG\LingoBackendAdapter';

	// server-local path to this file
	$dir = __DIR__;

	// register message file
	$GLOBALS[ 'wgMessagesDirs' ]['SemanticGlossary'] = $dir . '/i18n';
	$GLOBALS[ 'wgExtensionMessagesFiles' ]['SemanticGlossary'] = $dir . '/SemanticGlossary.i18n.php';

	// register class files with the Autoloader
	$autoloadClasses = array(
		'SG\PropertyRegistry'      => $dir . '/src/PropertyRegistry.php',
		'SG\Maintenance\GlossaryCacheRebuilder' => $dir . '/src/Maintenance/GlossaryCacheRebuilder.php',
		'SG\LingoBackendAdapter'         => $dir . '/src/LingoBackendAdapter.php',
		'SG\SemanticDataComparator'      => $dir . '/src/SemanticDataComparator.php',
		'SG\Cache\ElementsCacheBuilder'  => $dir . '/src/Cache/ElementsCacheBuilder.php',
		'SG\Cache\CacheInvalidator'      => $dir . '/src/Cache/CacheInvalidator.php',
		'SG\Cache\GlossaryCache'         => $dir . '/src/Cache/GlossaryCache.php',
	);

	$GLOBALS[ 'wgAutoloadClasses' ] = array_merge( $GLOBALS[ 'wgAutoloadClasses' ], $autoloadClasses );

	define( 'SG_PROP_GLT', 'Glossary-Term' );
	define( 'SG_PROP_GLD', 'Glossary-Definition' );
	define( 'SG_PROP_GLL', 'Glossary-Link' );
	define( 'SG_PROP_GLS', 'Glossary-Style' );

	/**
	 * Register properties
	 *
	 * @since 1.0
	 */
	$GLOBALS['wgHooks']['smwInitProperties'][] = function () {
		return \SG\PropertyRegistry::getInstance()->registerPropertiesAndAliases();
	};

	/**
	 * Invalidate on update
	 *
	 * @since 1.0
	 */
	$GLOBALS['wgHooks']['SMWStore::updateDataBefore'][] = function ( SMWStore $store, SMWSemanticData $semanticData ) {
		return \SG\Cache\CacheInvalidator::getInstance()->invalidateCacheOnStoreUpdate( $store, $semanticData );
	};

	/**
	 * Invalidate on delete
	 *
	 * @since 1.0
	 */
	$GLOBALS['wgHooks']['smwDeleteSemanticData'][] = function ( SMWDIWikiPage $subject ) {
		return \SG\Cache\CacheInvalidator::getInstance()->invalidateCacheOnPageDelete( smwfGetStore(), $subject );
	};

	/**
	 * Invalidate on title move
	 *
	 * @since 1.0
	 */
	$GLOBALS['wgHooks']['TitleMoveComplete'][] = function ( &$old_title, &$new_title, &$user, $pageid, $redirid ) {
		return \SG\Cache\CacheInvalidator::getInstance()->invalidateCacheOnPageMove( $old_title );
	};

} );
