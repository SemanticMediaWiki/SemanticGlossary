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

	/**
	 *
	 * @param $parser
	 * @param $text
	 * @return Boolean
	 */
	static function parse( &$parser, &$text ) {

		$sl = new SemanticGlossaryParser();
		$sl -> realParse( $parser, $text );

		return true;
	}

	/**
	 * Returns the list of terms applicable in the current context
	 * 
	 * @return Array an array mapping terms (keys) to descriptions (values)
	 */
	function getGlossaryArray( SemanticGlossaryMessageLog &$messages = null ) {

		global $smwgQDefaultNamespaces;

		$store = smwfGetStore(); // default store

		// Create query
		$desc = new SMWSomeProperty(SMWPropertyValue::makeProperty( '___glt'  ), new SMWThingDescription());
		$desc -> addPrintRequest(new SMWPrintRequest( SMWPrintRequest::PRINT_PROP, null, SMWPropertyValue::makeProperty( '___glt'  ) ));
		$desc -> addPrintRequest(new SMWPrintRequest( SMWPrintRequest::PRINT_PROP, null, SMWPropertyValue::makeProperty( '___gld'  ) ));
		$desc -> addPrintRequest(new SMWPrintRequest( SMWPrintRequest::PRINT_PROP, null, SMWPropertyValue::makeProperty( '___gll'  ) ));

		$query = new SMWQuery( $desc, true, false );
		$query -> querymode = SMWQuery::MODE_INSTANCES;

		global $smwgQDefaultLimit;
		$query -> setLimit( $smwgQDefaultLimit );
		$query -> sortkeys[ SG_PROP_GLT ] = 'ASC';

		// get the query result
		$queryresult = $store -> getQueryResult( $query );

		// assemble the result array
		$result = array( );
		while ( ( $resultline = $queryresult -> getNext() ) ) {

			$term = $resultline[ 0 ] -> getNextText( SMW_OUTPUT_HTML );
			$definition = $resultline[ 1 ] -> getNextText( SMW_OUTPUT_HTML );
			$link = $resultline[ 2 ] -> getNextText( SMW_OUTPUT_HTML );
			$subject = $resultline[ 0 ] -> getResultSubject();

			// FIXME: SMW has a bug that right after storing data this data
			// might be available twice. The workaround here is to compare the
			// first and second result and if they are identical assume that
			// it is because of the bug. (2nd condition in the if below)

			$nextTerm = $resultline[ 0 ] -> getNextText( SMW_OUTPUT_HTML );
			$nextDefinition = $resultline[ 1 ] -> getNextText( SMW_OUTPUT_HTML );

			// skip if more then one term or more than one definition present
			if ( ( $nextTerm || $nextDefinition ) &&
				!( $nextTerm == $term && $nextDefinition == $definition ) ) {

				if ( $messages ) {
					$messages -> addMessage(
						wfMsg('semanticglossary-termdefinedtwice', array($subject -> getPrefixedText())),
						SemanticGlossaryMessageLog::SG_WARNING );
				}

				continue;
			}

			$source = $subject -> getDBkeys();

			if ( array_key_exists( $term, $result ) ) {
				$result[ $term ] -> addDefinition( $definition, $link, $source );
			} else {
				$result[ $term ] = new SemanticGlossaryElement( $term, $definition, $link, $source );
			}
		}

		return $result;
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

		global $wgRequest, $sggSettings;

		$action = $wgRequest -> getVal( 'action', 'view' );
		if ( $text == null || $text == '' || $action == "edit" || $action == "ajax" || isset( $_POST[ 'wpPreview' ] ) )
			return true;
		// Get array of terms
		$terms = $this -> getGlossaryArray();

		if ( empty( $terms ) )
			return true;

		//Get the minimum length abbreviation so we don't bother checking against words shorter than that
		$min = min( array_map( 'strlen', array_keys( $terms ) ) );

//		var_export($text);
		//Parse HTML from page
//		$doc = new DOMDocument();
////		@$doc -> loadHTML( '<html><meta http-equiv="content-type" content="charset=utf-8"/>' . $text . '</html>' );
//		$doc -> loadHTML( $text );

		// this works in PHP 5.3.3. What about 5.1?
		$doc = @DOMDocument::loadHTML($text);

		//Find all text in HTML.
		$xpath = new DOMXpath( $doc );
		$elements = $xpath -> query( "//*[not(ancestor::*[@class='noglossary'])][text()!=' ']/text()" );

		//Iterate all HTML text matches
		$nb = $elements -> length;
		$changed = false;

		for ( $pos = 0; $pos < $nb; $pos++ ) {

			$el = &$elements -> item( $pos );

			if ( strlen( $el -> nodeValue ) < $min )
				continue;

			//Split node text into words, putting offset and text into $offsets[0] array
//			preg_match_all( "/\b[^\b\s\.,;:]+/", $el -> nodeValue, $offsets, PREG_OFFSET_CAPTURE );
			preg_match_all( "/[^\s$sggSettings->punctuationCharacters]+/", $el -> nodeValue, $offsets, PREG_OFFSET_CAPTURE );

			//Search and replace words in reverse order (from end of string backwards),
			//This way we don't mess up the offsets of the words as we iterate
			$len = count( $offsets[ 0 ] );

			for ( $i = $len - 1; $i >= 0; $i-- ) {

				$offset = $offsets[ 0 ][ $i ];

				//Check if word is an abbreviation from the terminologies
				if ( !is_numeric( $offset[ 0 ] ) && isset( $terms[ $offset[ 0 ] ] ) ) { //Word matches, replace with appropriate span tag
					$changed = true;

					$beforeMatchNode = $doc -> createTextNode( substr( $el -> nodeValue, 0, $offset[ 1 ] ) );
					$afterMatchNode = $doc -> createTextNode( substr( $el -> nodeValue, $offset[ 1 ] + strlen( $offset[ 0 ] ), strlen( $el -> nodeValue ) - 1 ) );

					//Wrap abbreviation in <span> tags
					$span = @$doc -> createElement( 'span' );
					$span -> setAttribute( 'class', "tooltip" );

					//Wrap abbreviation in <span> tags, hidden
					$spanAbr = @$doc -> createElement( 'span', $offset[ 0 ] );
					$spanAbr -> setAttribute( 'class', "tooltip_abbr" );

					//Wrap definition in <span> tags, hidden
					$spanTip = $terms[ $offset[ 0 ] ] -> getFullDefinition( $doc );
					$spanTip -> setAttribute( 'class', "tooltip_tip" );

					$el -> parentNode -> insertBefore( $beforeMatchNode, $el );
					$el -> parentNode -> insertBefore( $span, $el );
					$span -> appendChild( $spanAbr );
					$span -> appendChild( $spanTip );
					$el -> parentNode -> insertBefore( $afterMatchNode, $el );
					$el -> parentNode -> removeChild( $el );
					$el = $beforeMatchNode; //Set new element to the text before the match for next iteration
				}
			}
		}

		if ( $changed ) {
			$body = $xpath -> query( '/html/body' );
			$text = $doc -> saveHTML();
			$this -> loadModules( $parser );
		}

		return true;
	}

	protected function loadModules( &$parser ) {

		global $wgOut, $wgScriptPath;

		if ( defined( 'MW_SUPPORTS_RESOURCE_MODULES' ) ) {

			if ( !is_null( $parser ) ) {
				$parser -> getOutput() -> addModules( 'ext.SemanticGlossary' );
			} else {
				$wgOut -> addModules( 'ext.SemanticGlossary' );
			}
		} else {
			if ( !is_null( $parser ) && ( $wgOut -> isArticle() ) ) {
				$parser -> getOutput() -> addHeadItem( '<link rel="stylesheet" href="' . $wgScriptPath . '/extensions/SemanticGlossary/skins/SemanticGlossary.css" />', 'ext.SemanticGlossary.css' );
			} else {
				$wgOut -> addHeadItem( 'ext.SemanticGlossary.css', '<link rel="stylesheet" href="' . $wgScriptPath . '/extensions/SemanticGlossary/skins/SemanticGlossary.css" />' );
			}
		}
	}

}
