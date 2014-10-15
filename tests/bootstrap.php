<?php

if ( php_sapi_name() !== 'cli' ) {
	die( 'Not an entry point' );
}

if ( is_readable( $path = __DIR__ . '/../../SemanticMediaWiki/tests/autoloader.php' ) ) {
	print( "\nUsing SemanticMediaWiki ...\n" );
} else {
	die( 'The SemanticMediaWiki test autoloader is not available' );
}

$autoloader = require $path;

$autoloader->addPsr4( 'SG\\Tests\\', __DIR__ . '/phpunit' );

$autoloader->addClassMap( array(
	'SG\Maintenance\RebuildGlossaryCache' => __DIR__ . '/../maintenance/rebuildGlossaryCache.php',
) );
