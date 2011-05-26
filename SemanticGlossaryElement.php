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
	const SG_LINK = 3;

	private $mTerm;
	private $mFullDefinition = null;
	private $mDefinitions = array( );
	static private $mLinkTemplate = null;

	public function __construct ( $term=null, $definition=null, $link=null, $source=null ) {
		$this -> mTerm = $term;
		$this -> addDefinition( $definition, $link, $source );
	}

	public function addDefinition ( $definition=null, $link=null, $source=null ) {

		$this -> mDefinitions[ ] = array(
			self::SG_DEFINITION => $definition,
			self::SG_SOURCE => $source,
			self::SG_LINK => $link,
		);
	}

	public function getFullDefinition ( DOMDocument &$doc ) {

		// only create if not yet created
		if ( $this -> mFullDefinition == null ) {

			$this -> mFullDefinition = $doc -> createElement( 'span' );

			foreach ( $this -> mDefinitions as $definition ) {
				$element = $doc -> createElement( 'span', $definition[ self::SG_DEFINITION ] . ' ' );
				if ( $definition[ self::SG_LINK ] ) {
					$link = $this -> getLinkTemplate( $doc );
					$link -> setAttribute( 'href', Title::newFromText( $definition[ self::SG_LINK ] ) -> getFullURL() );
					$element -> appendChild( $link );
				}
				$this -> mFullDefinition -> appendChild( $element );
			}
		}

		return $this -> mFullDefinition -> cloneNode( true );
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

	public function getLink ( $key ) {
		return $this -> mDefinitions[ $key ][ self::SG_LINK ];
	}

	public function next () {
		next( $this -> mDefinitions );
	}

	private function getLinkTemplate ( DOMDocument &$doc ) {
		
		// create template if it does not yet exist
		if ( !self::$mLinkTemplate || ( self::$mLinkTemplate -> ownerDocument !== $doc ) ) {

			global $wgScriptPath;

			$linkimage = $doc -> createElement( 'img' );
			$linkimage -> setAttribute( "src", $wgScriptPath . '/extensions/SemanticGlossary/skins/linkicon.png' );

			self::$mLinkTemplate = $doc -> createElement( 'a' );
			self::$mLinkTemplate -> appendChild( $linkimage );
		}

		return self::$mLinkTemplate -> cloneNode( true );
	}

}
