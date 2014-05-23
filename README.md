# Semantic Glossary
[![Latest Stable Version](https://poser.pugx.org/mediawiki/semantic-glossary/version.png)](https://packagist.org/packages/mediawiki/semantic-glossary)
[![Packagist download count](https://poser.pugx.org/mediawiki/semantic-glossary/d/total.png)](https://packagist.org/packages/mediawiki/semantic-glossary)
[![Dependency Status](https://www.versioneye.com/php/mediawiki:semantic-glossary/badge.png)](https://www.versioneye.com/php/mediawiki:semantic-glossary)

The [Semantic Glossary][mw-semantic-glossary] (a.k.a SG) is a [Semantic MediaWiki][smw] extension where terms and abbreviations can be defined using semantic properties.

## Requirements

- PHP 5.3.2 or later
- MediaWiki 1.20 or later
- [Lingo extension][mw-lingo] 1.0 or later
- [Semantic MediaWiki][smw] 1.9 or later

## Installation

The recommended way to install this skin is by using [Composer][composer]. Just add the following to the MediaWiki `composer.json` file and run the `php composer.phar install/update` command.

```json
{
	"require": {
		"mediawiki/semantic-glossary": "~1.0"
	}
}
```

## Tests

This extension provides unit tests that can be run by a continues integration platform or manually by executing the `mw-phpunit-runner.php` script or [`phpunit`][mw-testing] together with the PHPUnit configuration file found in the root directory.

```sh
php mw-phpunit-runner.php [options]
```

## License

[GNU General Public License 2.0 or later][licence] or later.

[licence]: https://www.gnu.org/copyleft/gpl.html
[mw-semantic-glossary]: https://www.mediawiki.org/wiki/Extension:Semantic_Glossary
[mw-lingo]: https://www.mediawiki.org/wiki/Extension:Lingo
[smw]: https://www.mediawiki.org/wiki/Semantic_MediaWiki
[mw-testing]: https://www.mediawiki.org/wiki/Manual:PHP_unit_testing
[composer]: https://getcomposer.org/