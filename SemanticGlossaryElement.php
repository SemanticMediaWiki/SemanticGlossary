<?php

/**
 * File holding the SemanticGlossaryElement class.
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
 * This class represents a term-definition pair.
 * One term may be related to several definitions.
 *
 * @ingroup SemanticGlossary
 */
class SemanticGlossaryElement {
	const SG_DEFINITION = 1;
	const SG_SOURCE = 2;

	private $mTerm;
	private $mFullDefinition = null;
	private $mDefinitions = array( );

	public function __construct ( $term=null, $definition=null, $source=null ) {
		$this -> mTerm = $term;

		if ( $definition ) {
			$this -> addDefinition( $definition, $source );
		}
	}

	public function addDefinition ( $definition=null, $source=null ) {

		$this -> mDefinitions[ ] = array(
			self::SG_DEFINITION => $definition,
			self::SG_SOURCE => $source,
		);
	}

	public function getFullDefinition ( DOMDocument &$doc ) {

		if ( $this -> mFullDefinition == null ) {

			$this -> mFullDefinition = $doc -> createElement('span');

			foreach ( $this -> mDefinitions as $definition ) {
				$element = $doc -> createElement('span', $definition[ self::SG_DEFINITION ] );
				$this -> mFullDefinition -> appendChild( $element );
			}

		}

		return $this -> mFullDefinition -> cloneNode( true);
	}

	public function getCurrentKey () {
		return key( $this -> mDefinitions );
	}

	public function getSource ( $key ) {
		return $this -> mDefinitions[ $key ][ self::SG_SOURCE ];
	}

	public function getDefinition ( $key ) {
		return $this -> mDefinitions[ $key ][ self::SG_DEFINITION ];
	}

	public function next () {
		next( $this -> mDefinitions );
	}

}
