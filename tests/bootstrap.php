<?php

if ( PHP_SAPI !== 'cli' ) {
	die( 'Not an entry point' );
}

$autoloaderClassPath = __DIR__ . '/../../SemanticMediaWiki/tests/autoloader.php';

if ( !is_readable( $autoloaderClassPath ) ) {
	die( 'The SemanticMediaWiki test autoloader is not available' );
}

$autoloader = require $autoloaderClassPath;
$autoloader->addPsr4( 'SG\\Tests\\', __DIR__ . '/phpunit/Unit' );
$autoloader->addPsr4( 'SG\\Tests\\Integration\\', __DIR__ . '/phpunit/Integration' );
$autoloader->addPsr4( 'SMW\\Test\\', __DIR__ . '/../../SemanticMediaWiki/tests/phpunit' );
$autoloader->addPsr4( 'SMW\\Tests\\', __DIR__ . '/../../SemanticMediaWiki/tests/phpunit' );

$autoloader->addClassMap( [
	'SG\Maintenance\RebuildGlossaryCache' => __DIR__ . '/../maintenance/rebuildGlossaryCache.php'
] );
