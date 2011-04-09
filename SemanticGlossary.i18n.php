<?php
/**
 * Language file for Semantic Glossary
 */

$messages = array();

/** English
 * @author F.trott
 */
$messages['en'] = array(
	'semanticglossary-desc' => 'A terminology markup extension with a [http://semantic-mediawiki.org Semantic MediaWiki] backend',
	'semanticglossary-browsertitle' => 'Glossary',
	'semanticglossary-deleteselected' => 'Delete selected',
	'semanticglossary-savechanges' => 'Save changes',
	'semanticglossary-createnew' => 'Create new term',

	'semanticglossary-termsdefined' => 'These are the terms defined in the wiki:',
	'semanticglossary-notermsdefined' => 'There are currently no terms defined in the wiki.',
	'semanticglossary-enternewterm' => 'You can enter a new term and definition here:',

	'semanticglossary-messageheader' => 'Messages:',
	'semanticglossary-storedtermdefinedinarticle' => 'The term \'$1\' was originally defined in article [[$2]]. The definition was changed as required for now. However, as soon as the original article is edited again, the definition there takes precedence.',
	'semanticglossary-deletedtermdefinedinarticle' => 'The term \'$1\' was originally defined in article [[$2]]. The definition was deleted as required for now. However, as soon as the original article is edited again, the definition there takes precedence.',
	'semanticglossary-termdeleted' => 'Deleted $1.',
	'semanticglossary-storedtermdefinedtwice' => 'The article [[$1]] contains more than one property named $2. Will not store data for term \'$3\'.',
	'semanticglossary-termdefinedtwice' => 'The article [[$1]] contains more than one term and/or more than one definition. The entries will not be available for the glossary.',

	'semanticglossary-brokensession' => 'Action not allowed. Broken session data.',
	'semanticglossary-norights' => 'Action not allowed. Insufficient rights.',
	
	'___glt' => 'Glossary-Term',
	'___gld' => 'Glossary-Definition',

);

/** Message documentation (Message documentation)
 * @author F.trott
 */
$messages['qqq'] = array(
	'semanticglossary-desc' => '{{desc}}',
);

/** German (Deutsch)
 * @author F.trott
 */
$messages['de'] = array(
	'semanticglossary-desc' => 'A terminology markup extension with a [http://semantic-mediawiki.org Semantic MediaWiki] backend',
	'semanticglossary-browsertitle' => 'Glossar',
	'semanticglossary-deleteselected' => 'Ausgewählte löschen',
	'semanticglossary-savechanges' => 'Änderungen speichern',
	'semanticglossary-createnew' => 'Neuen Term anlegen',
	
	'semanticglossary-termsdefined' => 'Diese Begriffe sind im Wiki definiert:',
	'semanticglossary-notermsdefined' => 'Es sind derzeit keine Begriffe im Wiki definiert.',
	'semanticglossary-enternewterm' => 'Du kannst hier einen neuen Term mit Definition eingeben:',

	'semanticglossary-messageheader' => 'Meldungen:',

	'___glt' => 'Glossar-Term',
	'___gld' => 'Glossar-Definition',
);
