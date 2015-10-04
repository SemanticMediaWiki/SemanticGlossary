<?php

if ( php_sapi_name() !== 'cli' ) {
	die( 'Not an entry point' );
}

if ( !is_readable( $autoloaderClassPath = __DIR__ . '/../../SemanticMediaWiki/tests/autoloader.php' ) ) {
	die( 'The SemanticMediaWiki test autoloader is not available' );
}

print sprintf( "\n%-20s%s\n", "Semantic Glossary: ", SG_VERSION );

$autoloader = require $autoloaderClassPath;
$autoloader->addPsr4( 'SG\\Tests\\', __DIR__ . '/phpunit/Unit' );
$autoloader->addPsr4( 'SG\\Tests\\Integration\\', __DIR__ . '/phpunit/Integration' );

$autoloader->addClassMap( array(
	'SG\Maintenance\RebuildGlossaryCache' => __DIR__ . '/../maintenance/rebuildGlossaryCache.php',
) );
