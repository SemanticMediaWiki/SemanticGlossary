<?php

if ( PHP_SAPI !== 'cli' && PHP_SAPI !== 'phpdbg' ) {
	die( 'Not an entry point' );
}

error_reporting( E_ALL | E_STRICT );
date_default_timezone_set( 'UTC' );
ini_set( 'display_errors', 1 );

if ( !defined( 'SMW_PHPUNIT_AUTOLOADER_FILE' ) || !is_readable( SMW_PHPUNIT_AUTOLOADER_FILE ) ) {
	die( "\nThe Semantic MediaWiki test autoloader is not available" );
}

$width = 25;

if ( !defined( 'SMW_PHPUNIT_FIRST_COLUMN_WIDTH' ) ) {
	define( 'SMW_PHPUNIT_FIRST_COLUMN_WIDTH', $width );
}

$autoloader = require SMW_PHPUNIT_AUTOLOADER_FILE;
$autoloader->addPsr4( 'SG\\Tests\\', __DIR__ . '/phpunit/Unit' );
$autoloader->addPsr4( 'SG\\Tests\\Integration\\', __DIR__ . '/phpunit/Integration' );
$autoloader->addPsr4( 'SMW\\Test\\', __DIR__ . '/../../SemanticMediaWiki/tests/phpunit' );
$autoloader->addPsr4( 'SMW\\Tests\\', __DIR__ . '/../../SemanticMediaWiki/tests/phpunit' );

$autoloader->addClassMap( [
	'SG\Maintenance\RebuildGlossaryCache' => __DIR__ . '/../maintenance/rebuildGlossaryCache.php'
] );
