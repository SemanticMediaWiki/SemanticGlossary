<?php

/**
 * Lazy script to invoke the MediaWiki phpunit runner
 *
 * php <mw-phpunit-runner.php> [--coverage-clover|--coverage-html]
 */

if ( php_sapi_name() !== 'cli' ) {
	die( 'Not an entry point' );
}

print( "\nMediaWiki phpunit runnner ... \n" );

function isReadablePath( $path ) {

	if ( is_readable( $path ) ) {
		return $path;
	}

	throw new RuntimeException( "Expected an accessible {$path} path" );
}

function addArguments() {

	$arguments = null;
	$args = $GLOBALS['argv'];

	for ( $arg = reset( $args ); $arg !== false; $arg = next( $args ) ) {

		if ( $arg === '--coverage-clover' || $arg === '--coverage-html' ) {
			$arguments = $arg . ' ' . escapeshellarg( next( $args ) );
		}
	}

	return $arguments;
}

$mw = isReadablePath( __DIR__ . "/../../../tests/phpunit/phpunit.php" );
$config = isReadablePath( __DIR__ . "/../phpunit.xml.dist" );

passthru( "php {$mw} -c {$config} ". addArguments() );
