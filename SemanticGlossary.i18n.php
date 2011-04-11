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
	'semanticglossary-storedtermdefinedinarticle' => 'The term "$1" was originally defined in page [[$2]]. The definition was changed as required for now. However, as soon as the original page is edited again, the definition there takes precedence.',
	'semanticglossary-deletedtermdefinedinarticle' => 'The term "$1" was originally defined in page [[$2]]. The definition was deleted as required for now. However, as soon as the original page is edited again, the definition there takes precedence.',
	'semanticglossary-termdeleted' => 'Deleted $1.',
	'semanticglossary-storedtermdefinedtwice' => 'The page [[$1]] contains more than one property named $2. Will not store data for term "$3".',
	'semanticglossary-termdefinedtwice' => 'The page [[$1]] contains more than one term and/or more than one definition. The entries will not be available for the glossary.',

	'semanticglossary-brokensession' => 'Action not allowed. Broken session data.',
	'semanticglossary-norights' => 'Action not allowed. Insufficient rights.',

	'semanticglossary-prop-glt' => 'Glossary-Term',
	'semanticglossary-prop-gld' => 'Glossary-Definition',

);

/** Message documentation (Message documentation)
 * @author F.trott
 * @author Purodha
 */
$messages['qqq'] = array(
	'semanticglossary-desc' => '{{desc}}',
	'semanticglossary-prop-glt' => 'This is the name of a  [http://semantic-mediawiki.org/wiki/Property property] in the sense of [http://semantic-mediawiki.org/ Semantic MediaWiki].',
	'semanticglossary-prop-gld' => 'This is a name of a [http://semantic-mediawiki.org/wiki/Property property] in the sense of [http://semantic-mediawiki.org/ Semantic MediaWiki].',
);

/** Breton (Brezhoneg)
 * @author Fulup
 */
$messages['br'] = array(
	'semanticglossary-browsertitle' => 'Geriaoueg',
	'semanticglossary-deleteselected' => 'Dilemel ar re diuzet',
	'semanticglossary-savechanges' => "Enrollañ ar c'hemmoù",
	'semanticglossary-createnew' => 'Krouiñ un termen nevez',
	'semanticglossary-termsdefined' => 'Setu aze an termenoù termenet er wiki :',
	'semanticglossary-notermsdefined' => "N'eus bet termenet termen ebet er wiki evit poent.",
	'semanticglossary-enternewterm' => 'Gallout a rit merkañ un termen nevez gant e dermandur amañ :',
	'semanticglossary-messageheader' => 'Kemennadennoù :',
	'semanticglossary-termdeleted' => 'Diverket $1',
	'semanticglossary-termdefinedtwice' => "Muioc'h eget un termen ha/pe un termenadur zo er bajenn [[$1]]. N'hallo ket ar pennger-se bezañ implijet er c'heriaoueg.",
	'semanticglossary-norights' => "N'eo ket aotreet an ober-mañ. Re skort eo ho gwirioù.",
	'semanticglossary-prop-glt' => 'Geriaoueg-Termen',
	'semanticglossary-prop-gld' => 'Geriaoueg-Termenadur',
);

/** Bosnian (Bosanski)
 * @author CERminator
 */
$messages['bs'] = array(
	'semanticglossary-browsertitle' => 'Rječnik',
	'semanticglossary-deleteselected' => 'Obriši označeno',
	'semanticglossary-savechanges' => 'Spremi izmjene',
	'semanticglossary-createnew' => 'Napravi novi pojam',
	'semanticglossary-messageheader' => 'Poruke:',
	'semanticglossary-termdeleted' => 'Obrisano $1.',
	'semanticglossary-norights' => 'Akcija nije dopuštena. Nemate dovoljno prava.',
	'semanticglossary-prop-glt' => 'Rječnički-Pojam',
	'semanticglossary-prop-gld' => 'Rječnička-Definicija',
);

/** German (Deutsch)
 * @author F.trott
 * @author Kghbln
 */
$messages['de'] = array(
	'semanticglossary-desc' => 'Ermöglicht die Erstellung und Nutzung eines Glossars mit [http://semantic-mediawiki.org/wiki/Semantic_MediaWiki_–_Startseite Semantic MediaWiki] als Basis',
	'semanticglossary-browsertitle' => 'Glossar',
	'semanticglossary-deleteselected' => 'Ausgewählte löschen',
	'semanticglossary-savechanges' => 'Änderungen speichern',
	'semanticglossary-createnew' => 'Neuen Begriff anlegen',
	'semanticglossary-termsdefined' => 'Diese Begriffe sind im Wiki definiert:',
	'semanticglossary-notermsdefined' => 'Es sind derzeit keine Begriffe im Wiki definiert.',
	'semanticglossary-enternewterm' => 'Hier kann ein neuer Begriff mit seiner Definition eingegeben werden:',
	'semanticglossary-messageheader' => 'Meldungen:',
	'semanticglossary-storedtermdefinedinarticle' => 'Der Begriff „$1“ wurde ursprünglich auf Seite [[$2]] definiert. Die Definition wurde vorerst entsprechend den Eingaben geändert. Allerdings wird, sobald die ursprüngliche Seite bearbeitet wird, die dort angegebene Definition wieder den Vorrang erhalten.',
	'semanticglossary-deletedtermdefinedinarticle' => 'Der Begriff „$1“ wurde ursprünglich auf Seite [[$2]] definiert. Die Definition wurde vorerst gelöscht. Allerdings wird, sobald die ursprüngliche Seite bearbeitet wird, die dort angegebene Definition wieder den Vorrang erhalten.',
	'semanticglossary-termdeleted' => 'Der Begriff „$1“ wurde gelöscht.',
	'semanticglossary-storedtermdefinedtwice' => 'Die Seite [[$1]] enthält mehr als ein Attribut namens $2. Die Daten für den Begriff „$3“ werden nicht gespeichert.',
	'semanticglossary-termdefinedtwice' => 'Die Seite [[$1]] enthält mehr als einen Begriff und/ oder mehr als eine Definition. Die Eingaben werden daher nicht im Glossar verfügbar sein.',
	'semanticglossary-brokensession' => 'Diese Aktion ist nicht zulässig: Abgelaufene Sitzungsdaten.',
	'semanticglossary-norights' => 'Diese Aktion ist nicht zulässig: Unzureichende Berechtigungen.',
	'semanticglossary-prop-glt' => 'Glossar (Begriff)',
	'semanticglossary-prop-gld' => 'Glossar (Definition)',
);

/** Interlingua (Interlingua)
 * @author McDutchie
 */
$messages['ia'] = array(
	'semanticglossary-desc' => 'Un extension pro marcation de terminologia con un base de [http://semantic-mediawiki.org Semantic MediaWiki]',
	'semanticglossary-browsertitle' => 'Glossario',
	'semanticglossary-deleteselected' => 'Deler selection',
	'semanticglossary-savechanges' => 'Salveguardar modificationes',
	'semanticglossary-createnew' => 'Crear nove termino',
	'semanticglossary-termsdefined' => 'Istes es le terminos definite in iste wiki:',
	'semanticglossary-notermsdefined' => 'Actualmente il non ha terminos definite in le wiki.',
	'semanticglossary-enternewterm' => 'Tu pote entrar un nove termino e definition hic:',
	'semanticglossary-messageheader' => 'Messages:',
	'semanticglossary-storedtermdefinedinarticle' => 'Le termino "$1" esseva originalmente definite in le pagina [[$2]]. Le definition esseva cambiate como necessari pro le momento. Nonobstante, si tosto que le pagina original es modificate de novo, le definition de illo habera le precedentia.',
	'semanticglossary-deletedtermdefinedinarticle' => 'Le termino "$1" esseva originalmente definite in le pagina [[$2]]. Le definition esseva delite como necessari pro le momento. Nonobstante, si tosto que le pagina original es modificate de novo, le definition de illo habera le precedentia.',
	'semanticglossary-termdeleted' => '$1 delite.',
	'semanticglossary-storedtermdefinedtwice' => 'Le pagina [[$1]] contine plus de un proprietate con nomine $2. Le datos pro le termino "$3" non essera immagazinate.',
	'semanticglossary-termdefinedtwice' => 'Le pagina [[$1]] contine plus de un termino e/o plus de un definition. Le entratas non essera disponibile in le glossario.',
	'semanticglossary-brokensession' => 'Action non permittite. Datos de session defectuose.',
	'semanticglossary-norights' => 'Action non permittite. Derectos insufficiente.',
	'semanticglossary-prop-glt' => 'Termino de glossario',
	'semanticglossary-prop-gld' => 'Definition de glossario',
);

/** Colognian (Ripoarisch)
 * @author Purodha
 */
$messages['ksh'] = array(
	'semanticglossary-desc' => 'E Zohsazprojramm för Bejreffe ze makeere med_enem [http://semantic-mediawiki.org Semantesch MediaWiki] Hengerjrond.',
	'semanticglossary-browsertitle' => 'Wööterverzeischneß',
	'semanticglossary-deleteselected' => 'Donn de ußjewählte fottschmieße!',
	'semanticglossary-savechanges' => 'Änderunge faßhallde',
	'semanticglossary-createnew' => 'Donn ene neue Bejreff aanlääje',
	'semanticglossary-termsdefined' => 'Heh di Bejreffe sin em Wiki singem Wööterverzeischneß:',
	'semanticglossary-notermsdefined' => 'Em Momang sinn_er kein Bejreffe em Wiki singem Wööterverzeischneß faßjehallde.',
	'semanticglossary-enternewterm' => 'Heh kam_mer ne neuje Bejreff enjävve un ussenander possemänteere, wat dä bedügg:',
	'semanticglossary-messageheader' => 'Nohreschte:',
	'semanticglossary-storedtermdefinedinarticle' => 'Di Bedügdenis vun däm Bejreff „$1“ wood et eets op dä Sigg „[[$2]]“ ussenander possemänteert.
Jäz eß di Bedüggdeneß wi nüüdesch aanjepaß woode.
Allerdengs, wann di orschprönglesche Sigg norr_ens verändert weed, kritt dat, wat doh shtund, widder der Vörrang.',
	'semanticglossary-deletedtermdefinedinarticle' => 'Di Bedüggdeneß vun däm Bejreff „$1“ wood et eets op dä Sigg „[[$2]]“ ussenander possemänteert.
Jäz eß di Bedüggdeneß wi nüüdesch fott jeschmeße woode.
Allerdengs, esubalt di orschprönglesche Sigg norr_ens verändert weed, kritt dat, wat doh shtund, widder der Vörrang.',
	'semanticglossary-termdeleted' => '„$1“ es fottjeschmeße.',
	'semanticglossary-storedtermdefinedtwice' => 'En dä Sigg „[[$1]]“ es mieh wi ein Eijeschaff mem Naame „$2“ dren. Di Daate för dä Bejreff „$3“ wääde nit faßjehallde.',
	'semanticglossary-termdefinedtwice' => 'En dä Sigg „[[$1]]“ es mieh wie eine Bejreff, udder miehj wi ein Bedüggdeneß usseneijn possemänteert, udder beeds. Di wääde nit em Wööter_Verzeishneß faßjehallde.',
	'semanticglossary-brokensession' => 'Dat jeiht nit. Ding Daate fum Enlogge sin fott.',
	'semanticglossary-norights' => 'Dat es nit zohjelohße.
Ding Zohjreffsrräschte ricke doh nit för.',
	'semanticglossary-prop-glt' => 'Bejreff em Wööterverzeijschneß',
	'semanticglossary-prop-gld' => 'Bedüggdeneß em Wööterverzeischneß',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'semanticglossary-browsertitle' => 'Glossaire',
	'semanticglossary-savechanges' => 'Ännerunge späicheren',
	'semanticglossary-createnew' => 'Neie Begrëff uleeën',
	'semanticglossary-messageheader' => 'Messagen:',
);

/** Macedonian (Македонски)
 * @author Bjankuloski06
 */
$messages['mk'] = array(
	'semanticglossary-desc' => 'Додаток за означување на терминологија за [http://semantic-mediawiki.org Семантички МедијаВики]',
	'semanticglossary-browsertitle' => 'Поимник',
	'semanticglossary-deleteselected' => 'Избриши одбрано',
	'semanticglossary-savechanges' => 'Зачувај промени',
	'semanticglossary-createnew' => 'Создај нова поим',
	'semanticglossary-termsdefined' => 'Еве ги поимите определени во викито:',
	'semanticglossary-notermsdefined' => 'Моментално нема поими определени во викито.',
	'semanticglossary-enternewterm' => 'Тука можете да внесете нов поим и значење:',
	'semanticglossary-messageheader' => 'Пораки:',
	'semanticglossary-storedtermdefinedinarticle' => 'Поимот „$1“ бил првично утврден на страницата [[$2]]. Толкувањето е сменето според тековните потреби. Но штом повторно ќе се уреди изворната страница, тамошното толкување добива предност.',
	'semanticglossary-deletedtermdefinedinarticle' => 'Поимот „$1“ бил првично утврден на страницата [[$2]]. Толкувањето е избришано според тековните потреби. Но штом повторно ќе се уреди изворната страница, тамошното толкување добива предност.',
	'semanticglossary-termdeleted' => 'Го избришав $1.',
	'semanticglossary-storedtermdefinedtwice' => 'Страницата [[$1]] содржи повеќе од едно својство со име $2. Нема да зачувам податоци за поимот „$3“.',
	'semanticglossary-termdefinedtwice' => 'Оваа страница [[$1]] содржи повеќе од еден поим и/или повеќе од едно толкување. Овие записи нема да бидат достапни во поимникот.',
	'semanticglossary-brokensession' => 'Дејството не е допуштено. Сесиските податоци се расипани.',
	'semanticglossary-norights' => 'Дејството не е допуштено. Немате доволно права.',
	'semanticglossary-prop-glt' => 'Поимник-Поим',
	'semanticglossary-prop-gld' => 'Поимник-Толкување',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'semanticglossary-desc' => 'Begrippenlijst die gebruik maakt van [http://semantic-mediawiki.org Semantic MediaWiki]',
	'semanticglossary-browsertitle' => 'Begrippenlijst',
	'semanticglossary-deleteselected' => 'Selectie verwijderen',
	'semanticglossary-savechanges' => 'Wijzigingen opslaan',
	'semanticglossary-createnew' => 'Nieuwe term aanmaken',
	'semanticglossary-termsdefined' => 'Dit zijn de in de wiki gedefinieerde begrippen:',
	'semanticglossary-notermsdefined' => 'Er zijn momenteel geen begrippen gedefinieerd in de wiki.',
	'semanticglossary-enternewterm' => 'U kunt hier een nieuw begrip met definitie hier invoeren:',
	'semanticglossary-messageheader' => 'Berichten:',
);

/** Pashto (پښتو)
 * @author Ahmed-Najib-Biabani-Ibrahimkhel
 */
$messages['ps'] = array(
	'semanticglossary-browsertitle' => 'وييپانګه',
	'semanticglossary-deleteselected' => 'ټاکل شوی ړنګول',
	'semanticglossary-savechanges' => 'بدلونونه خوندي کول',
	'semanticglossary-messageheader' => 'پيغامونه:',
);

/** Serbian Cyrillic ekavian (‪Српски (ћирилица)‬)
 * @author Rancher
 */
$messages['sr-ec'] = array(
	'semanticglossary-browsertitle' => 'Речник',
	'semanticglossary-deleteselected' => 'Обриши изабрано',
	'semanticglossary-savechanges' => 'Сачувај измене',
	'semanticglossary-createnew' => 'Направи нови појам',
	'semanticglossary-termsdefined' => 'Ово су појмови одређени у викију:',
	'semanticglossary-notermsdefined' => 'Нема појмова одређених у викију.',
	'semanticglossary-enternewterm' => 'Можете унети нови појам и дефиницију овде:',
	'semanticglossary-messageheader' => 'Поруке:',
	'semanticglossary-storedtermdefinedinarticle' => 'Појам „$1“ је изворно одређен у страници [[$2]]. Дефиниција је промењена. Када се изворна страница поново измени, дефиниција ће стећи предност.',
	'semanticglossary-deletedtermdefinedinarticle' => 'Појам „$1“ је изворно одређен у страници [[$2]]. Дефиниција је обрисана. Када се изворна страница поново измени, дефиниција ће стећи предност.',
	'semanticglossary-termdeleted' => 'Обрисано $1.',
	'semanticglossary-storedtermdefinedtwice' => 'Страница [[$1]] садржи више од једног својства с називом $2. Она неће смештати податке за појам „$3“.',
	'semanticglossary-termdefinedtwice' => 'Страница [[$1]] садржи више од једног појма и дефиниције. Уноси неће бити доступни речнику.',
	'semanticglossary-brokensession' => 'Радња није дозвољена. Подаци о сесији су изгубљени.',
	'semanticglossary-norights' => 'Радња није дозвољена. Немате потребна права.',
);

/** Telugu (తెలుగు)
 * @author Veeven
 */
$messages['te'] = array(
	'semanticglossary-deleteselected' => 'ఎంచుకున్నవాటిని తొలగించు',
	'semanticglossary-savechanges' => 'మార్పులను భద్రపరచు',
	'semanticglossary-messageheader' => 'సందేశాలు:',
	'semanticglossary-norights' => 'చర్యను అనుమతించము. తగినన్ని అధికారాలు లేవు.',
);

/** Tagalog (Tagalog)
 * @author AnakngAraw
 */
$messages['tl'] = array(
	'semanticglossary-desc' => 'Isang minarkahang paitaas na dugtong sa terminolohiya na may isang dulong panlikod na [http://semantic-mediawiki.org Semantikong MediaWiki]',
	'semanticglossary-browsertitle' => 'Glosaryo',
	'semanticglossary-deleteselected' => 'Burahin ang napili',
	'semanticglossary-savechanges' => 'Sagipin ang mga pagbabago',
	'semanticglossary-createnew' => 'Lumikha ng bagong kataga',
	'semanticglossary-termsdefined' => 'Ito ang mga katagang binigyan ng kahulugan sa loob ng wiki:',
	'semanticglossary-notermsdefined' => 'Pangkasalukuyang walang mga katagang binigyan ng kahulugan sa loob ng wiki.',
	'semanticglossary-enternewterm' => 'Makapagpapasok ka rito ng isang bagong kataga at kahulugan:',
	'semanticglossary-messageheader' => 'Mga mensahe:',
	'semanticglossary-storedtermdefinedinarticle' => 'Ang katagang "$1" ay orihinal na binigyan ng kahulugan sa pahinang [[$2]].  Binago ang kahulugan ayon sa pangangailangan para sa ngayon.  Subalit, kapag nabago na ulit ang orihinal na pahina, ang kahulugan doon ang magkakamit ng mas mahigit na kahalagan.',
	'semanticglossary-deletedtermdefinedinarticle' => 'Ang katagang "$1" ay orihinal na nabigyan ng kahulugan sa pahina [[$2]].  Nabura ang kahulugan ayon sa pangangailangan sa ngayon.  Subalit, kapag binago na ulit ang orihinal na pahina, ang kahulugan doon ang magkakamit ng mas mahigit na kahalagahan.',
	'semanticglossary-termdeleted' => 'Binura ang $1.',
	'semanticglossary-storedtermdefinedtwice' => 'Ang pahinang [[$1]] ay naglalaman ng mas mahigit kaysa isang ari-arian na pinangalanang $2.  Hindi mag-iimbak ng dato para sa katagang "$3".',
	'semanticglossary-termdefinedtwice' => 'Ang pahinang [[$1]] ay naglalaman ng mas mahigit kaysa isang kataga at/o mas mahigit pa sa isang kahulugan.  Ang mga pagpapasok ay hindi makukuha para sa glosaryo.',
	'semanticglossary-brokensession' => 'Hindi pinapayagan ang kilos.  Sira ang dato ng inilaang panahon.',
	'semanticglossary-norights' => 'Hindi pinapahintulutan ang galaw.  Hindi sapat ang mga karapatan.',
);

