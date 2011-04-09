<?php

/**
 * File holding the default settings for the Semantic Glossary extension
 *
 * @author Stephan Gambke
 *
 * @file
 * @ingroup SemanticGlossary
 */
if ( !defined( 'SG_VERSION' ) ) {
	die( 'This file is part of the Semantic Glossary extension, it is not a valid entry point.' );
}

/**
 * Class to encapsulate Semantic Glossary settings
 * @ingroup SemanticGlossary
 */
class SemanticGlossarySettings {
	
	/**
	 * @var Contains the characters that may not be part of a term.
	 */
	public $punctuationCharacters = '\.(),;:?!';
}

