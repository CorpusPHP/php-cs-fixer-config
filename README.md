# PHP-CS-Fixer Configuration

[![Latest Stable Version](https://poser.pugx.org/corpus/php-cs-fixer-config/version)](https://packagist.org/packages/corpus/php-cs-fixer-config)
[![License](https://poser.pugx.org/corpus/php-cs-fixer-config/license)](https://packagist.org/packages/corpus/php-cs-fixer-config)
[![ci.yml](https://github.com/CorpusPHP/php-cs-fixer-config/actions/workflows/ci.yml/badge.svg)](https://github.com/CorpusPHP/php-cs-fixer-config/actions/workflows/ci.yml)


This package shares the PHP-CS-Fixer rules used by Corpus projects. It does not
choose files to fix. Each project owns its own Finder.

## Requirements

- **php**: ^7.4 || ~8.0.0 || ~8.1.0 || ~8.2.0 || ~8.3.0 || ~8.4.0 || ~8.5.0 || ~8.6.0
- **friendsofphp/php-cs-fixer**: ^3.95

## Installation

Install the latest version with:

```bash
composer require --dev 'corpus/php-cs-fixer-config'
```

`friendsofphp/php-cs-fixer` is a package dependency because the factory creates
its `PhpCsFixer\Config` object. It is not a production dependency of consuming
applications because this package is installed with `--dev`.

## Usage

Create `.php-cs-fixer.dist.php` in the consuming project:

```php
<?php

use Corpus\PhpCsFixer\Config;
use PhpCsFixer\Finder;

return Config::php7x4()
	->setFinder(
		Finder::create()->in(__DIR__ . '/src'),
	);
```

Choose a factory for the lowest PHP version supported by the project:

- `Config::php7x4()`
- `Config::php8x1()`
- `Config::php8x2()`
- `Config::php8x5()`

The factory preserves the shared rules and enables risky rules. It uses tabs,
Unix line endings, and the standard PHP-CS-Fixer cache. Each version adds only
rules that are safe for that PHP target.

## Project-specific overrides

Use PHP-CS-Fixer's normal API after creating the configuration:

```php
<?php

use Corpus\PhpCsFixer\Config;
use PhpCsFixer\Finder;

$config = Config::php8x2();

return $config
	->setRules([
		...$config->getRules(),
		'ordered_imports' => false,
	])
	->setFinder(Finder::create()->in(__DIR__));
```

## Updating

Update this package with Composer. Then run PHP-CS-Fixer in each consuming
project and review the resulting formatting changes before committing them.

```sh
composer update corpus/php-cs-fixer-config
composer exec php-cs-fixer fix -- --diff
```

Rule changes can reformat existing files. Pin a release version in projects
that need controlled upgrades.

## Development

The package checks its own code with Corpus Coding Standard. Run all checks
with:

```sh
make test
```

The package itself uses the PHP 7.4 configuration. This keeps the distributed
source compatible with the package minimum PHP version.

Apply automatic fixes with Corpus Coding Standard and PHP-CS-Fixer:

```sh
make fix
```

The Composer plugin installs the Coding Standard for PHP_CodeSniffer.