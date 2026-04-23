This file contains the RELEASE-NOTES of the Semantic Glossary (a.k.a. SG) extension.

### 6.0.0

Released on March 13, 2026.

* New minimum required versions:
    * PHP 8.1 and later
	* MediaWiki 1.43 and later
    * Semantic MediaWiki 6.0 and later   
	* Lingo: 3.2.3 and later
* Translation updates from translatewiki.net
* Query optimization for glossary terms with spaces ([#85](https://github.com/SemanticMediaWiki/SemanticGlossary/issues/85))
* Added deduplicated fix
* Fix "creation of dynamic property is deprecated", thank you ArchiXL
* Fixed glossary terms with spaces and multiple glossary terms, thank you @YOUR1 ([#93](https://github.com/SemanticMediaWiki/SemanticGlossary/pull/93))
* Re-enable rebuildGlossaryCache maintenance script and fix tests ([dbdec05](https://github.com/SemanticMediaWiki/SemanticGlossary/commit/dbdec05277a6823b13df2b8cc23f378cd233d768)))
* improve SMW 6 compatibility

### 5.0.1

Released on 2025-03-12.

* New minimum required versions:
	* MediaWiki 1.39 
	* Lingo: 3.2.3
* Translation updates from translatewiki.net
* Improved glossary searching by using the given 'search terms' passed by Lingo
* Support for newer MediaWiki versions
* Support for newer Semantic MediaWiki versions
* Improved testing and CI

### 4.0.0

Released on 2021-07-09.

* New minimum required versions:
	* PHP 7.1
	* MediaWiki 1.31
	* Semantic MediaWiki 3.1
	* Lingo 3.1
* Compatibility with Semantic MediaWiki 3.1
* Compatibility with MediaWiki 1.35
* Fixes for loading the Lingo dependency
* Translation updates from translatewiki.net

### 3.0.0

Released on 2018-10-09.

* New minimum required versions:
	* PHP 5.6
	* MediaWiki 1.27
* Compatibility with Semantic MediaWiki 3.0
* Compatibility with MediaWiki 1.31
* Compatibility with Lingo 3.0
* Translation updates from translatewiki.net

### 2.2.0

Released on 2017-05-24.

* Requires Lingo 2.0.3 or above
	* Fixed fatal error: Call to undefined function Lingo\string()
	* [#24](https://github.com/SemanticMediaWiki/SemanticGlossary/issues/24) Fixed missing link icon
	* [#25](https://github.com/SemanticMediaWiki/SemanticGlossary/issues/25) Fixed broken Special:Preferences
* Translation updates from translatewiki.net

### 2.1.0

Released on 2017-03-27.

* Compatibility with Semantic MediaWiki 2.5.x
* Semantic MediaWiki 2.4 as minimum requirement
* Translation updates from translatewiki.net

### 2.0.1

Released on 2016-05-24.

* Enable installation from a tarball or zip file
* Translation updates from translatewiki.net

### 2.0.0

Released on 2016-03-09.

* New minimum required versions:
	* MediaWiki 1.26
	* Semantic MediaWiki 2.3
	* Lingo 2.0
* Translation updates from translatewiki.net
* Use the new extension registration mechanism introduced in MediwWiki 1.25
* Rework registration of properties and MW hooks
* Use autoloader provided by Composer (PSR-4)

### 1.1.2

Released on 2015-09-26.

* Use `QueryResult::getCountValue` where available to make it compliant with Semantic Mediawiki 2.3
* Translation updates from translatewiki.net

### 1.1.1

Released on 2014-10-15.

* Improved bootstrap test autoloader

### 1.1.0

Released on 2014-08-10.

* Added support for Semantic Mediawiki 2.0
* Translation updates from translatewiki.net
* Added maintenance script "rebuildGlossaryCache.php" to rebuild glossary cache and update pages that contain a glossary term annotation
* Extended refactoring of the codebase
* I18n-system migrated from PHP- to JSON files (by Siebrand Mazeland)

### 1.0.0

Released on 2014-03-04.

* Started using semantic versioning
* Added caching
* Support setting per-term CSS styles added (by Nathan Douglas)
* Support synonyms introduced (by Yevheniy Vlasenko during Google Summer of Code 2013)
* Support the Composer dependency manager for PHP

### 0.1

Released on 2011-10-30.

* Initial release
