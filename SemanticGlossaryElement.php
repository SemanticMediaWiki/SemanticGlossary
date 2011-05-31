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
	const SG_TERM = 0;
	const SG_DEFINITION = 1;
	const SG_SOURCE = 2;
	const SG_LINK = 3;

	private $mFullDefinition = null;
	private $mDefinitions = array();
	private $mTerm = null;
	static private $mLinkTemplate = null;

	public function __construct( &$term, &$definition = null ) {

		$this->mTerm = $term;

		if ( $definition ) {
			$this->addDefinition( $definition );
		}
	}

	public function addDefinition( &$definition ) {
		$this->mDefinitions[] = $definition;
	}

	public function getFullDefinition( DOMDocument &$doc ) {
		// only create if not yet created
		if ( $this->mFullDefinition == null || $this->mFullDefinition->ownerDocument !== $doc ) {

			// Wrap term and definition in <span> tags
			$span = $doc->createElement( 'span' );
			$span->setAttribute( 'class', 'tooltip' );

			// Wrap term in <span> tag, hidden
			$spanTerm = $doc->createElement( 'span', $this->mTerm );
			$spanTerm->setAttribute( 'class', 'tooltip_abbr' );

			// Wrap definition in two <span> tags
			$spanDefinitionOuter = $doc->createElement( 'span' );
			$spanDefinitionOuter->setAttribute( 'class', 'tooltip_tipwrapper' );

			$spanDefinitionInner = $doc->createElement( 'span' );
			$spanDefinitionInner->setAttribute( 'class', 'tooltip_tip' );

			foreach ( $this->mDefinitions as $definition ) {
				$element = $doc->createElement( 'span', htmlentities( $definition[self::SG_DEFINITION], ENT_COMPAT, 'UTF-8' ) . ' ' );
				if ( $definition[self::SG_LINK] ) {
					$linkedTitle = Title::newFromText( $definition[self::SG_LINK] );
					if ( $linkedTitle ) {
						$link = $this->getLinkTemplate( $doc );
						$link->setAttribute( 'href', $linkedTitle->getFullURL() );
						$element->appendChild( $link );
					}
				}
				$spanDefinitionInner->appendChild( $element );
			}

			// insert term and definition
			$span->appendChild( $spanTerm );
			$span->appendChild( $spanDefinitionOuter );
			$spanDefinitionOuter->appendChild( $spanDefinitionInner );

			$this->mFullDefinition = $span;

		}

		return $this->mFullDefinition->cloneNode( true );
	}

	public function getCurrentKey() {
		return key( $this->mDefinitions );
	}

	public function getTerm( $key ) {
		return $this->mDefinitions[$key][self::SG_TERM];
	}

	public function getSource( &$key ) {
		return $this->mDefinitions[$key][self::SG_SOURCE];
	}

	public function getDefinition( &$key ) {
		return $this->mDefinitions[$key][self::SG_DEFINITION];
	}

	public function getLink( &$key ) {
		return $this->mDefinitions[$key][self::SG_LINK];
	}

	public function next() {
		next( $this->mDefinitions );
	}

	private function getLinkTemplate( DOMDocument &$doc ) {
		// create template if it does not yet exist
		if ( !self::$mLinkTemplate || ( self::$mLinkTemplate->ownerDocument !== $doc ) ) {
			global $wgScriptPath;

			$linkimage = $doc->createElement( 'img' );
			$linkimage->setAttribute( 'src', $wgScriptPath . '/extensions/SemanticGlossary/skins/linkicon.png' );

			self::$mLinkTemplate = $doc->createElement( 'a' );
			self::$mLinkTemplate->appendChild( $linkimage );
		}

		return self::$mLinkTemplate->cloneNode( true );
	}

}
