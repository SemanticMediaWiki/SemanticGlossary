<?php
/**
 * Special page aliases for Semantic Glossary
 */

$specialPageAliases = array();

/** English (English) */
$specialPageAliases['en'] = array(
	'SemanticGlossaryBrowser' => array( 'Glossary' ),
);

/** Arabic (العربية) */
$specialPageAliases['ar'] = array(
	'SemanticGlossaryBrowser' => array( 'قاموس' ),
);

/** German (Deutsch) */
$specialPageAliases['de'] = array(
	'SemanticGlossaryBrowser' => array( 'Glossar' ),
);

/** Macedonian (Македонски) */
$specialPageAliases['mk'] = array(
	'SemanticGlossaryBrowser' => array( 'Поимник' ),
);

/** Dutch (Nederlands) */
$specialPageAliases['nl'] = array(
	'SemanticGlossaryBrowser' => array( 'Woordenlijst' ),
);

/**
 * For backwards compatibility with MediaWiki 1.15 and earlier.
 */
$aliases =& $specialPageAliases;