<?php

/**
 * File holding the SemanticGlossaryBackend class
 *
 * @author Stephan Gambke
 * @file
 * @ingroup SemanticGlossary
 */
if ( !defined( 'SG_VERSION' ) ) {
	die( 'This file is part of the SemanticGlossary extension, it is not a valid entry point.' );
}

/**
 * The SemanticGlossaryBackend class.
 *
 * @ingroup SemanticGlossary
 */
class SemanticGlossaryBackend {

	protected $mQueryResult;
	protected $mResultLine;
	protected $mMessageLog;
	protected $mTerm;
	protected $mDefinition;
	protected $mLink;
	protected $mSource;

	public function __construct ( SemanticGlossaryMessageLog &$messages = null ) {

		$this -> mMessageLog = $messages;

		$store = smwfGetStore(); // default store
		// Create query
		$desc = new SMWSomeProperty( new SMWDIProperty( '___glt' ), new SMWThingDescription() );
		$desc -> addPrintRequest( new SMWPrintRequest( SMWPrintRequest::PRINT_PROP, null, SMWPropertyValue::makeProperty( '___glt' ) ) );
		$desc -> addPrintRequest( new SMWPrintRequest( SMWPrintRequest::PRINT_PROP, null, SMWPropertyValue::makeProperty( '___gld' ) ) );
		$desc -> addPrintRequest( new SMWPrintRequest( SMWPrintRequest::PRINT_PROP, null, SMWPropertyValue::makeProperty( '___gll' ) ) );

		$query = new SMWQuery( $desc, false, false );
		$query -> sort = true;
		$query -> sortkeys[ '___glt' ] = 'ASC';

		// get the query result
		$this -> mQueryResult = $store -> getQueryResult( $query );
	}

	public function next () {

		// find next line
		while ( $resultline = $this -> mQueryResult -> getNext() ) {
			$this -> mTerm = $resultline[ 0 ] -> getNextText( SMW_OUTPUT_HTML );
			$this -> mDefinition = $resultline[ 1 ] -> getNextText( SMW_OUTPUT_HTML );
			$this -> mLink = $resultline[ 2 ] -> getNextText( SMW_OUTPUT_HTML );
			$this -> mSource = $resultline[ 0 ] -> getResultSubject() -> getTitle() -> getPrefixedText();

			$nextTerm = $resultline[ 0 ] -> getNextText( SMW_OUTPUT_HTML );
			$nextDefinition = $resultline[ 1 ] -> getNextText( SMW_OUTPUT_HTML );
			$nextLink = $resultline[ 2 ] -> getNextText( SMW_OUTPUT_HTML );


			// FIXME: SMW has a bug that right after storing data this data
			// might be available twice. The workaround here is to compare the
			// first and second result and if they are identical assume that
			// it is because of the bug. (2nd condition in the if below)
			// skip if more then one term or more than one definition present
			if ( ( $nextTerm || $nextDefinition || $nextLink ) &&
				!( $nextTerm == $this -> mTerm && $nextDefinition == $this -> mDefinition && $nextLink == $this -> mLink ) ) {

				if ( $this -> mMessageLog ) {
					$this -> mMessageLog -> addMessage(
						wfMsg( 'semanticglossary-termdefinedtwice', array( $subject -> getTitle() -> getPrefixedText() ) ),
						SemanticGlossaryMessageLog::SG_WARNING );
				}

				continue;
			}

			return true;
		}

		return $resultline != null;
	}

	function &getTerm () {
		return $this -> mTerm;
	}

	function &getDefinition () {
		return $this -> mDefinition;
	}

	function &getLink () {
		return $this -> mLink;
	}

	function &getSource () {
		return $this -> mSource;
	}

}

