<?php

/**
 * File holding the SemanticGlossaryMessageLog class.
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
 * This class holds messages (errors, warnings, notices) for Semantic Glossary
 *
 * Contains a static function to initiate the parsing.
 *
 * @ingroup SemanticGlossary
 */
class SemanticGlossaryMessageLog {

	private $mMessages = array();
	private $parser = null;

	const SG_ERROR = 1;
	const SG_WARNING = 2;
	const SG_NOTICE = 3;

	function addMessage( $message, $severity = self::SG_NOTICE ) {
		$this->mMessages[] = array( $message, $severity );
	}

	function addError( $message ) {
		$this->mMessages[] = array( $message, self::SG_ERROR );
	}

	function addWarning( $message ) {
		$this->mMessages[] = array( $message, self::SG_WARNING );
	}

	function addNotice( $message ) {
		$this->mMessages[] = array( $message, self::SG_NOTICE );
	}

	function getMessagesFormatted( $severity = self::SG_WARNING, $header = null ) {
		global $wgTitle, $wgUser;

		$ret = '';

		if ( $header == null ) {
			$header = wfMsg( 'semanticglossary-messageheader' );
		}

		foreach ( $this->mMessages as $message ) {
			if ( $message[1] <= $severity ) {
				$ret .= '* ' . $message[0] . "\n";
			}
		}

		if ( $ret != '' ) {
			if ( !$this->parser ) {
				$parser = new Parser();
			}

			$ret = Html::rawElement( 'div', array( 'class' => 'messages' ),
					Html::rawElement( 'div', array( 'class' => 'heading' ), $header ) .
					$parser->parse( $ret, $wgTitle, ParserOptions::newFromUser( $wgUser ) )->getText()
			);
		}

		return $ret;
	}

}

