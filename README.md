# Semantic Glossary

[![Build Status](https://travis-ci.org/SemanticMediaWiki/SemanticGlossary.svg)](https://travis-ci.org/SemanticMediaWiki/SemanticGlossary)
[![Code Coverage](https://scrutinizer-ci.com/g/SemanticMediaWiki/SemanticGlossary/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/SemanticMediaWiki/SemanticGlossary/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/SemanticMediaWiki/SemanticGlossary/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/SemanticMediaWiki/SemanticGlossary/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/mediawiki/semantic-glossary/version.png)](https://packagist.org/packages/mediawiki/semantic-glossary)
[![Packagist download count](https://poser.pugx.org/mediawiki/semantic-glossary/d/total.png)](https://packagist.org/packages/mediawiki/semantic-glossary)
[![Dependency Status](https://www.versioneye.com/php/mediawiki:semantic-glossary/badge.png)](https://www.versioneye.com/php/mediawiki:semantic-glossary)

The [Semantic Glossary][mw-semantic-glossary] (a.k.a SG) is a [Semantic MediaWiki][smw] extension where
terms and abbreviations can be defined using semantic properties.

## Requirements

- PHP 5.3.3 or later
- MediaWiki 1.26 or later
- [Semantic MediaWiki][smw] 2.4 or later

## Installation

The recommended way to install this extension is by using [Composer][composer].
Just add the following to the MediaWiki `composer.local.json` file and run the
`php composer.phar install/update mediawiki/semantic-glossary` command.

```json
{
	"require": {
		"mediawiki/semantic-glossary": "~2.1"
	}
}
```

(Alternatively you can download a tar ball or zip file from
[GitHub](https://github.com/SemanticMediaWiki/SemanticGlossary/releases/latest)
and extract it into the `extensions` directory of your MediaWiki installation.)

Then add the following line to your `LocalSettings.php`:
```php
wfLoadExtension('SemanticGlossary');
```

It is *NOT* necessary to install the Lingo extension separately. Doing so will
result in errors.

## Contribution and support

If you want to contribute work to the project please subscribe to the developers mailing list and
have a look at the contribution guideline.

* Ask a question on [the mailing list](https://semantic-mediawiki.org/wiki/Mailing_list)
* Ask a question on the #semantic-mediawiki IRC channel on Freenode.

## Tests

This extension provides unit and integration tests that are run by a [continues integration platform][travis]
but can also be executed using `composer phpunit` from the extension base directory.

## License

[GNU General Public License 2.0][license] or later.

[license]: https://www.gnu.org/copyleft/gpl.html
[mw-semantic-glossary]: https://www.mediawiki.org/wiki/Extension:Semantic_Glossary
[mw-lingo]: https://www.mediawiki.org/wiki/Extension:Lingo
[smw]: https://www.mediawiki.org/wiki/Semantic_MediaWiki
[composer]: https://getcomposer.org/
[travis]: https://travis-ci.org/SemanticMediaWiki/SemanticGlossary
