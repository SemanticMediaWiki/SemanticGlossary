<?php

/**
 * File holding the SpecialSemanticGlossaryBrowser class.
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
 * This class creates and processes Special:GlossaryBrowser.
 *
 * Includable special pages have an execute() function which can be
 * called from either context, so to parse text within them, it's
 * necessary to check $this->mIncluding to determine the correct function
 * to use.
 *
 * @todo Write this class.
 * @ingroup SemanticGlossary
 */
class SpecialSemanticGlossaryBrowser extends SpecialPage {

	private $mMessages;

	function __construct() {
		parent::__construct( 'SemanticGlossaryBrowser' );
		$this -> mMessages = new SemanticGlossaryMessageLog();
	}

	function execute( $subpage ) {
		global $wgRequest, $wgOut, $wgUser;

		// preparation stuff
		$this->setHeaders();
		$this->loadModules();

		$hasEditRights = $wgUser->isAllowed( 'editglossary' );

		if ( $this->isActionAllowed() ) {
			if ( $wgRequest->getText( 'submit' ) != null ) {
				// if the form was submitted, store the data
				$this->actionStoreData();
			} elseif ( $wgRequest->getText( 'createnew' ) != null ) {
				// if a new term was defined, create it
				$this->actionCreateNewTerm();
			} elseif ( $wgRequest->getText( 'delete' ) != null ) {
				// if a new term was defined, create it
				$this->actionDeleteData();
			}
		}

		// get the glossary data
		$parser = new SemanticGlossaryParser();
		$glossaryarray = $parser->getGlossaryArray( $this->mMessages );

		// set function to create a table row (textareas when editing is
		// allowed, else normal text)
		if ( $hasEditRights ) {
			$createTableRowMethod = 'createTableRowForEdit';
		} else {
			$createTableRowMethod = 'createTableRowForDisplay';
		}

		// create HTML fragment for table rows
		$tablerows = '';

		// loop through all terms
		foreach ( $glossaryarray as $term => $glossaryElement ) {
			// One term may have several definitions. Include them all.
			while ( ( $key = $glossaryElement->getCurrentKey() ) !== null ) {
				$source = $glossaryElement->getSource( $key );
				$definition = $glossaryElement->getDefinition( $key );
				$link = $glossaryElement->getLink( $key );

				$tablerows .= $this->$createTableRowMethod( $source, $term, $definition, $link );

				$glossaryElement->next();
			}
		}

		if ( $tablerows != '' ) {
			$listOfTermsFragment =
				Html::rawElement( 'table', null,
					Html::rawElement( 'tbody', null, $tablerows )
			);

			if ( $hasEditRights ) {
				// append action buttons
				$listOfTermsFragment .=
					Html::element( 'input', array( 'type' => 'submit', 'name' => 'delete', 'value' => wfMsg( 'semanticglossary-deleteselected' ), 'accesskey' => 'd' ) ) .
					Html::element( 'input', array( 'type' => 'submit', 'name' => 'submit', 'value' => wfMsg( 'semanticglossary-savechanges' ), 'accesskey' => 's' ) );
			}

			$listOfTermsFragment =
				Html::rawElement( 'div', array( 'class' => 'termslist' ),
					Html::element( 'div', array( 'class' => 'heading' ), wfMsg( 'semanticglossary-termsdefined' ) ) .
					$listOfTermsFragment
			);
		} else {
			$listOfTermsFragment =
				Html::rawElement( 'div', array( 'class' => 'termslist' ),
					Html::element( 'div', array( 'class' => 'heading' ), wfMsg( 'semanticglossary-notermsdefined' ) )
			);
		}

		// From here on no more errors should occur. Create list of errors.
		$errorsFragment = $this->mMessages->getMessagesFormatted( SemanticGlossaryMessageLog::SG_NOTICE );

		if ( $errorsFragment ) {
			$errorsFragment .= Html::rawElement( 'hr' );
		}

		if ( $hasEditRights ) {
			// create form fragment to allow input of a new term
			$newTermFragment =
				Html::rawElement( 'hr' ) .
				Html::rawElement( 'div', array( 'class' => 'newterm' ),
					Html::rawElement( 'div', array( 'class' => 'heading' ), wfMsg( 'semanticglossary-enternewterm' ) ) .
					Html::rawElement( 'table', null,
						Html::rawElement( 'tbody', null,
							Html::rawElement( 'tr', array( 'class' => 'row' ),
								Html::rawElement( 'td', array( 'class' => 'termcell' ),
									Html::element( 'textarea', array( 'name' => 'newterm' ) )
								) .
								Html::rawElement( 'td', array( 'class' => 'definitioncell' ),
									Html::rawElement( 'div', array( 'class' => 'definitionareawrapper' ),
										Html::element( 'textarea', array( 'name' => 'newdefinition' ) )
									)
								) .
								Html::rawElement( 'td', array( 'class' => 'linkcell' ),
									Html::element( 'textarea', array( 'name' => 'newlink' ) )
								)
							)
						)
					) .
					Html::element( 'input', array( 'type' => 'submit', 'name' => 'createnew', 'value' => wfMsg( 'semanticglossary-createnew' ), 'accesskey' => 'n' ) )
			);

			$salt = rand( 10000, 99999 );
			$editTokenFragment = Html::rawElement( 'input', array( 'type' => 'hidden', 'name' => 'editToken', 'value' => $wgUser -> editToken( $salt ) . $salt ) );

			// assemble output
			$output =
				Html::rawElement( 'div', array( 'class' => 'glossarybrowser' ),
					$errorsFragment .
					Html::rawElement( 'form', array( 'method' => 'POST' ),
						$listOfTermsFragment .
						$newTermFragment .
						$editTokenFragment
					)
			);
		} else {
			// assemble output
			$output =
				Html::rawElement( 'div', array( 'class' => 'glossarybrowser' ),
					$errorsFragment .
					$listOfTermsFragment
			);
		}

		$wgOut->addHTML( $output );
	}

	/**
	 * Returns the name that goes in the <h1> in the special page itself, and also the name that
	 * will be listed in Special:Specialpages
	 *
	 * @return String
	 */
	function getDescription() {
		return wfMsg( 'semanticglossary-browsertitle' );
	}

	/**
	 * Loads the CSS file for the GlossaryBrowser Special page
	 */
	protected function loadModules() {
		global $wgOut, $wgScriptPath;

		if ( defined( 'MW_SUPPORTS_RESOURCE_MODULES' ) ) {
			$wgOut->addModules( 'ext.SemanticGlossary.Browser' );
		} else {
			$wgOut->addHeadItem( 'ext.SemanticGlossary.Browser.css', '<link rel="stylesheet" href="' . $wgScriptPath . '/extensions/SemanticGlossary/skins/SemanticGlossaryBrowser.css" />' );
		}
	}

	/**
	 * Gets data from wgRequest and stores it
	 */
	protected function actionStoreData() {
		global $wgRequest;

		// get ass array of input values
		$inputdata = $wgRequest -> getValues();

		// loop through all input values
		foreach ( $inputdata as $key => $value ) {

			// only consider terms here, other parameters are accessed by name
			if ( substr( $key, -5 ) == ':term' ) {
				// cut off ':term'
				$pageString = substr( $key, 0, -5 );

				// new data
				$newTerm = $value;
				$newDefinition = $inputdata[$pageString . ':definition'];
				$newLink = $inputdata[$pageString . ':link'];

				$page = $this->getPageObjectFromInputName( $pageString );

				// get its data
				$pageData = smwfGetStore()->getSemanticData( $page );


				// get old values
				$oldTerm = $this->getPropertyFromData( $pageData, '___glt' );
				if ( $oldTerm === false ) {
					continue;
				}

				$oldDefinition = $this->getPropertyFromData( $pageData, '___gld' );
				if ( $oldDefinition === false ) {
					continue;
				}

				$oldLink = $this->getPropertyFromData( $pageData, '___gll' );
				if ( $oldLink === false ) {
					continue;
				}

				// only store data if anything changed
				if ( $newTerm != $oldTerm ||
					$newDefinition != $oldDefinition ||
					$newLink != $oldLink
				) {
					$this -> updateData( $page, array(
						'___glt' => ( $newTerm ? new SMWDIString( $newTerm ) : null ),
						'___gld' => ( $newDefinition ? new SMWDIBlob( $newDefinition ) : null ),
						'___gll' => ( $newLink ? new SMWDIString( $newLink ) : null )
						) );

					// issue a warning if the original definition is on a real page
					$title = $page->getTitle();
					if ( $title->isKnown() ) {
						$this->mMessages->addMessage(
							wfMsg( 'semanticglossary-storedtermdefinedinarticle', array( $oldTerm, $title->getPrefixedText() ) ),
							SemanticGlossaryMessageLog::SG_WARNING
						);
					} else {
						$this->mMessages->addMessage(
							wfMsg( 'semanticglossary-termchanged', array( $oldTerm ) ),
							SemanticGlossaryMessageLog::SG_NOTICE
						);
					}
				}
			}
		}
	}

	protected function actionCreateNewTerm() {
		global $wgRequest;

		$newTerm = $wgRequest->getText( 'newterm' );

		if ( $newTerm == null || $newTerm == '' ) {
			$this->mMessages->addMessage( 'Term was empty. Nothing created.', SemanticGlossaryMessageLog::SG_WARNING );
			return;
		}

		$newDefinition = $wgRequest->getText( 'newdefinition' );
		$newLink = $wgRequest->getText( 'newlink' );

		$page = $this->findNextPageName();

		// store data
		$this -> updateData( $page, array(
			'___glt' => ( $newTerm ? new SMWDIString( $newTerm ) : null ),
			'___gld' => ( $newDefinition ? new SMWDIBlob( $newDefinition ) : null ),
			'___gll' => ( $newLink ? new SMWDIString( $newLink ) : null )
			) );

		$this->mMessages->addMessage(
			wfMsg( 'semanticglossary-termadded', array( $newTerm ) ),
			SemanticGlossaryMessageLog::SG_NOTICE
		);
	}

	protected function actionDeleteData() {
		global $wgRequest;

		// get ass array of input values
		$inputdata = $wgRequest->getValues();

		foreach ( $inputdata as $key => $value ) {
			// only consider checkboxes here
			if ( substr( $key, -8 ) == ':checked' ) {
				// cut off ':checked'
				$pageString = substr( $key, 0, -8 );

				$page = $this->getPageObjectFromInputName( $pageString );

				$this->updateData( $page, array(
					'___glt' => null,
					'___gld' => null,
					'___gll' => null,
				) );

				$oldTerm = $wgRequest->getVal( $pageString . ':term' );

				$title = $page->getTitle();
				if ( $title && $title->isKnown() ) {
					$this->mMessages->addMessage(
						wfMsg( 'semanticglossary-deletedtermdefinedinarticle', array( $oldTerm, $title->getPrefixedText() ) ),
						SemanticGlossaryMessageLog::SG_WARNING
					);
				} else {
					$this->mMessages->addMessage(
						wfMsg( 'semanticglossary-termdeleted', array( $oldTerm ) ),
						SemanticGlossaryMessageLog::SG_NOTICE
					);
				}
			}
		}
	}

	protected function getPropertyFromData( SMWSemanticData &$pageData, $propertyName ) {
		$property = new SMWDIProperty( $propertyName );
		$propertyValues = $pageData->getPropertyValues( $property );

		if ( count( $propertyValues ) == 1 ) {
			return $propertyValues[0]->getString();
		} elseif ( count( $propertyValues ) > 1 ) {
			if ( count( $propertyValues ) > 1 ) {
				$this->mMessages->addMessage(
					wfMsg( 'semanticglossary-storedtermdefinedtwice', array( $pageData->getSubject()->getPrefixedText(), $propertyName, $newTerm ) ),
					SemanticGlossaryMessageLog::SG_ERROR
				);
			}
			return false;
		} else {
			return null;
		}
	}

	protected function getPageObjectFromInputName( $pageString ) {
		// split the source string into interwiki reference, namespace and page title
		$matches = array();
		preg_match( '/^(.*):(.*):(.*)$/', $pageString, $matches );

		// create SMWWikiPageValue (SMW's wiki page representation)
		return new SMWDIWikiPage( $matches[3], $matches[2], $matches[1] );
	}

	// find unused SMW page
	protected function findNextPageName() {
		$termPages = smwfGetStore()->getAllPropertySubjects( new SMWDIProperty( '___glt' ) );
		$defPages = smwfGetStore()->getAllPropertySubjects( new SMWDIProperty( '___gld' ) );
		$linkPages = smwfGetStore()->getAllPropertySubjects( new SMWDIProperty( '___gll' ) );

		$pages = array();

		foreach ( $termPages as $page ) {
			$pages[$page->getDBkey()] = $page->getDBkey();
		}

		foreach ( $defPages as $page ) {
			$pages[$page->getDBkey()] = $page->getDBkey();
		}

		foreach ( $linkPages as $page ) {
			$pages[$page->getDBkey()] = $page->getDBkey();
		}

		$termNumber = count( $pages );


		while ( array_key_exists( "GlossaryTerm#$termNumber", $pages ) ) {
			$termNumber++;
		}

		return new SMWDIWikiPage( "GlossaryTerm#$termNumber", NS_MAIN, '' );

		exit();
	}

	protected function updateData( SMWDIWikiPage &$page, array $data ) {
		$newData = new SMWSemanticData( $page, false );

		$oldData = smwfGetStore()->getSemanticData( $page );
		$oldProps = $oldData->getProperties();

		// get properties, replace as requested, retain other properties
		foreach ( $oldProps as $property ) {
			$propertyID = $property->getKey();

			if ( array_key_exists( $propertyID, $data ) ) {
				// set new data if defined, else ignore property (i.e. delete property from page)
				if ( $data[$propertyID] != null ) {
					$newData->addPropertyObjectValue( $property, $data[$propertyID] );
				}

				unset( $data[$propertyID] );
			} else {
				$values = $oldData->getPropertyValues( $property );
				foreach ( $values as $value ) {
					$newData->addPropertyObjectValue( $property, $value );
				}
			}
		}

		// store properties that were not present before, i.e. properties
		// remaining in $data
		foreach ( $data as $propertyID => $propertyValue ) {
			// set new data if defined, else ignore property (i.e. do not set property on this page)
			if ( $data[$propertyID] != null ) {
				$property = new SMWDIProperty( $propertyID );
				$newData -> addPropertyObjectValue( $property, $data[$propertyID] );
			}

			unset( $data[$propertyID] );
		}

		// finally store the updated page data
		smwfGetStore()->doDataUpdate( $newData );
	}

	private function createTableRowForEdit( $source, $term, $definition, $link ) {
		return
		Html::rawElement( 'tr', array( 'class' => 'row' ),
			Html::rawElement( 'td', array( 'class' => 'actioncell' ),
				Html::input( "$source:checked", 'true', 'checkbox' )
			) .
			Html::rawElement( 'td', array( 'class' => 'termcell' ),
				Html::textarea( "$source:term", $term )
			) .
			Html::rawElement( 'td', array( 'class' => 'definitioncell' ),
				Html::rawElement( 'div', array( 'class' => 'definitionareawrapper' ),
					Html::textarea( "$source:definition", $definition )
				)
			) .
			Html::rawElement( 'td', array( 'class' => 'linkcell' ),
				Html::textarea( "$source:link", $link )
			)
		);
	}

	private function createTableRowForDisplay( $source, $term, $definition, $link ) {
		return
		Html::rawElement( 'tr', array( 'class' => 'row' ),
			Html::rawElement( 'td', array( 'class' => 'termcell' ), $term ) .
			Html::rawElement( 'td', array( 'class' => 'definitioncell' ), $definition ) .
			Html::rawElement( 'td', array( 'class' => 'linkcell' ), $link )
		);
	}

	/**
	 * Checks if the user wants to perform an action, has the necessary right
	 * and submitted a valid edit token.
	 *
	 * @return Boolean
	 */
	private function isActionAllowed() {
		global $wgRequest, $wgUser;

		$editTokenWithSalt = $wgRequest->getText( 'editToken' );
		$actionRequested = ( $editTokenWithSalt != null );

		if ( $actionRequested ) { // user wants to perform an action
			if ( $wgUser->isAllowed( 'editglossary' ) ) { // user has the necessary right
				$editTokenAndSaltArray = explode( EDIT_TOKEN_SUFFIX, $editTokenWithSalt );
				$tokenValid = $wgUser->matchEditTokenNoSuffix(
					$editTokenAndSaltArray[0],
					$editTokenAndSaltArray[1]
				);

				if ( $tokenValid ) { // edit token is valid
					return true;
				} else {
					$this->mMessages->addMessage(
						wfMsg( 'semanticglossary-brokensession' ),
						SemanticGlossaryMessageLog::SG_ERROR
					);
				}
			} else {
				$this->mMessages->addMessage(
					wfMsg( 'semanticglossary-norights' ),
					SemanticGlossaryMessageLog::SG_ERROR
				);
			}
		}

		// user does not want to perform an action
		// OR does not have the rights
		// OR did not submit a valid edit token
		return false;
	}

}
