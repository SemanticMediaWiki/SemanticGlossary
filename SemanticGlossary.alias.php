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
	'SemanticGlossaryBrowser' => array( 'Semantisches_Glossar' ),
);

/** Persian (فارسی) */
$specialPageAliases['fa'] = array(
	'SemanticGlossaryBrowser' => array( 'واژه‌نامه' ),
);

/** Luxembourgish (Lëtzebuergesch) */
$specialPageAliases['lb'] = array(
	'SemanticGlossaryBrowser' => array( 'Glossaire' ),
);

/** Macedonian (Македонски) */
$specialPageAliases['mk'] = array(
	'SemanticGlossaryBrowser' => array( 'Поимник' ),
);

/** Nedersaksisch (Nedersaksisch) */
$specialPageAliases['nds-nl'] = array(
	'SemanticGlossaryBrowser' => array( 'Woordelieste' ),
);

/** Dutch (Nederlands) */
$specialPageAliases['nl'] = array(
	'SemanticGlossaryBrowser' => array( 'Woordenlijst' ),
);

/** Polish (Polski) */
$specialPageAliases['pl'] = array(
	'SemanticGlossaryBrowser' => array( 'Słowniczek' ),
);

/**
 * For backwards compatibility with MediaWiki 1.15 and earlier.
 */
$aliases =& $specialPageAliases;