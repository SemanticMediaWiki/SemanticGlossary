<?php

if ( php_sapi_name() !== 'cli' ) {
	die( 'Not an entry point' );
}

if ( !is_readable( $autoloaderClassPath = __DIR__ . '/../../SemanticMediaWiki/tests/autoloader.php' ) ) {
	die( 'The SemanticMediaWiki test autoloader is not available' );
}

if ( !class_exists( 'SemanticGlossary' ) || ( $version = SemanticGlossary::getVersion() ) === null ) {
	die( 'SemanticGlossary is not registered via wfLoadExtension, please adapt your LocalSettings.' );
}

print sprintf( "\n%-20s%s\n", "Semantic Glossary: ", $version );

$autoloader = require $autoloaderClassPath;
$autoloader->addPsr4( 'SG\\Tests\\', __DIR__ . '/phpunit/Unit' );
$autoloader->addPsr4( 'SG\\Tests\\Integration\\', __DIR__ . '/phpunit/Integration' );

$autoloader->addClassMap( array(
	'SG\Maintenance\RebuildGlossaryCache' => __DIR__ . '/../maintenance/rebuildGlossaryCache.php',
) );
