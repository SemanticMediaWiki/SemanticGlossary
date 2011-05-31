<?php

/**
 * File holding the SemanticGlossaryParser class.
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
 * This class parses the given text and enriches it with definitions for defined
 * terms.
 *
 * Contains a static function to initiate the parsing.
 *
 * @ingroup SemanticGlossary
 */
class SemanticGlossaryParser {

	private $mGlossaryArray = null;
	private $mGlossaryTree = null;
	private static $parserSingleton = null;

	/**
	 *
	 * @param $parser
	 * @param $text
	 * @return Boolean
	 */
	static function parse( &$parser, &$text ) {
		wfProfileIn( __METHOD__ );

		if ( !self::$parserSingleton ) {
			self::$parserSingleton = new SemanticGlossaryParser();
		}

		self::$parserSingleton->realParse( $parser, $text );

		wfProfileOut( __METHOD__ );

		return true;
	}

	function getBackend() {
		return new SemanticGlossaryBackend();
	}

	/**
	 * Returns the list of terms applicable in the current context
	 *
	 * @return Array an array mapping terms (keys) to descriptions (values)
	 */
	function getGlossaryArray( SemanticGlossaryMessageLog &$messages = null ) {
		wfProfileIn( __METHOD__ );

		// build glossary array only once per request
		if ( !$this->mGlossaryArray ) {
			$this->buildGlossary( $messages );
		}

		wfProfileOut( __METHOD__ );

		return $this->mGlossaryArray;
	}

	/**
	 * Returns the list of terms applicable in the current context
	 *
	 * @return Array an array mapping terms (keys) to descriptions (values)
	 */
	function getGlossaryTree( SemanticGlossaryMessageLog &$messages = null ) {
		wfProfileIn( __METHOD__ );

		// build glossary array only once per request
		if ( !$this->mGlossaryTree ) {
			$this->buildGlossary( $messages );
		}

		wfProfileOut( __METHOD__ );

		return $this->mGlossaryTree;
	}

	protected function buildGlossary( SemanticGlossaryMessageLog &$messages = null ) {
		wfProfileIn( __METHOD__ );

		$this->mGlossaryTree = new SemanticGlossaryTree();

		$backend = $this->getBackEnd();

		// assemble the result array
		$this->mGlossaryArray = array();
		while ( $backend->next() ) {

			$elementData = array(
				SemanticGlossaryElement::SG_TERM => ( $term = $backend->getTerm() ),
				SemanticGlossaryElement::SG_DEFINITION => $backend->getDefinition(),
				SemanticGlossaryElement::SG_LINK => $backend->getLink(),
				SemanticGlossaryElement::SG_SOURCE => $backend->getSource()
			);

			if ( array_key_exists( $term, $this->mGlossaryArray ) ) {
				$this->mGlossaryArray[$term]->addDefinition( $elementData );
			} else {
				$this->mGlossaryArray[$term] = new SemanticGlossaryElement( $term, $elementData );
			}

			$this->mGlossaryTree->addTerm( $term, $elementData );
		}

		wfProfileOut( __METHOD__ );
	}

	/**
	 * Parses the given text and enriches applicable terms
	 *
	 * This method currently only recognizes terms consisting of max one word
	 *
	 * @param $parser
	 * @param $text
	 * @return Boolean
	 */
	protected function realParse( &$parser, &$text ) {
		global $wgRequest;

		wfProfileIn( __METHOD__ );

		$action = $wgRequest->getVal( 'action', 'view' );

		if ( $text == null ||
			$text == '' ||
			$action == 'edit' ||
			$action == 'ajax' ||
			isset( $_POST['wpPreview'] )
		) {

			wfProfileOut( __METHOD__ );
			return true;
		}

		// Get array of terms
		$glossary = $this->getGlossaryTree();

		if ( $glossary == null ) {
			wfProfileOut( __METHOD__ );
			return true;
		}

		// Parse HTML from page
		// @todo FIXME: this works in PHP 5.3.3. What about 5.1?
		wfProfileIn( __METHOD__ . ' 1 loadHTML' );
		wfSuppressWarnings();

		$doc = DOMDocument::loadHTML(
				'<html><meta http-equiv="content-type" content="charset=utf-8"/>' . $text . '</html>'
		);

		wfRestoreWarnings();
		wfProfileOut( __METHOD__ . ' 1 loadHTML' );

		wfProfileIn( __METHOD__ . ' 2 xpath' );
		// Find all text in HTML.
		$xpath = new DOMXpath( $doc );
		$elements = $xpath->query(
				"//*[not(ancestor-or-self::*[@class='noglossary'] or ancestor-or-self::a)][text()!=' ']/text()"
		);
		wfProfileOut( __METHOD__ . ' 2 xpath' );

		// Iterate all HTML text matches
		$nb = $elements->length;
		$changedDoc = false;

		for ( $pos = 0; $pos < $nb; $pos++ ) {
			$el = $elements->item( $pos );

			if ( strlen( $el->nodeValue ) < $glossary->getMinTermLength() ) {
				continue;
			}

			wfProfileIn( __METHOD__ . ' 3 lexer' );
			$matches = array();
			preg_match_all(
				'/[[:alpha:]]+|[^[:alpha:]]/u',
				$el->nodeValue,
				$matches,
				PREG_OFFSET_CAPTURE | PREG_PATTERN_ORDER
			);
			wfProfileOut( __METHOD__ . ' 3 lexer' );

			if ( count( $matches ) == 0 || count( $matches[0] ) == 0 ) {
				continue;
			}

			$lexemes = &$matches[0];
			$countLexemes = count( $lexemes );
			$parent = &$el->parentNode;
			$index = 0;
			$changedElem = false;

			while ( $index < $countLexemes ) {
				wfProfileIn( __METHOD__ . ' 4 findNextTerm' );
				list( $skipped, $used, $definition ) =
					$glossary->findNextTerm( $lexemes, $index, $countLexemes );
				wfProfileOut( __METHOD__ . ' 4 findNextTerm' );

				wfProfileIn( __METHOD__ . ' 5 insert' );
				if ( $used > 0 ) { // found a term
					if ( $skipped > 0 ) { // skipped some text, insert it as is
						$parent->insertBefore(
							$doc->createTextNode(
								substr( $el->nodeValue,
									$currLexIndex = $lexemes[$index][1],
									$lexemes[$index + $skipped][1] - $currLexIndex )
							),
							$el
						);
					}

					$parent->insertBefore( $definition->getFullDefinition( $doc ), $el );

					$changedElem = true;
				} else { // did not find term, just use the rest of the text
					// If we found no term now and no term before, there was no
					// term in the whole element. Might as well not change the
					// element at all.
					// Only change element if found term before
					if ( $changedElem ) {
						$parent->insertBefore(
							$doc->createTextNode(
								substr( $el->nodeValue, $lexemes[$index][1] )
							),
							$el
						);
					} else {
						wfProfileOut( __METHOD__ . ' 5 insert' );
						// In principle superfluous, the loop would run out
						// anyway. Might save a bit of time.
						break;
					}
				}
				wfProfileOut( __METHOD__ . ' 5 insert' );

				$index += $used + $skipped;
			}

			if ( $changedElem ) {
				$parent->removeChild( $el );
				$changedDoc = true;
			}
		}

		if ( $changedDoc ) {
			$body = $xpath->query( '/html/body' );

			$text = '';
			foreach ( $body->item( 0 )->childNodes as $child ) {
				$text .= $doc->saveXML( $child );
			}

			$this->loadModules( $parser );
		}

		wfProfileOut( __METHOD__ );

		return true;
	}

	protected function loadModules( &$parser ) {
		global $wgOut, $wgScriptPath;

		if ( defined( 'MW_SUPPORTS_RESOURCE_MODULES' ) ) {
			if ( !is_null( $parser ) ) {
				$parser->getOutput()->addModules( 'ext.SemanticGlossary' );
			} else {
				$wgOut->addModules( 'ext.SemanticGlossary' );
			}
		} else {
			if ( !is_null( $parser ) && ( $wgOut->isArticle() ) ) {
				$parser->getOutput()->addHeadItem( '<link rel="stylesheet" href="' . $wgScriptPath . '/extensions/SemanticGlossary/skins/SemanticGlossary.css" />', 'ext.SemanticGlossary.css' );
			} else {
				$wgOut->addHeadItem( 'ext.SemanticGlossary.css', '<link rel="stylesheet" href="' . $wgScriptPath . '/extensions/SemanticGlossary/skins/SemanticGlossary.css" />' );
			}
		}
	}

}

