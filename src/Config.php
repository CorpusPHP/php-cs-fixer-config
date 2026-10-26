<?php

namespace Corpus\PhpCsFixer;

use PhpCsFixer\Config as PhpCsFixerConfig;

final class Config {

	public static function php7x4(): PhpCsFixerConfig {
		return self::build(Rules::php7x4());
	}

	public static function php8x1(): PhpCsFixerConfig {
		return self::build(Rules::php8x1());
	}

	public static function php8x2(): PhpCsFixerConfig {
		return self::build(Rules::php8x2());
	}

	public static function php8x5(): PhpCsFixerConfig {
		return self::build(Rules::php8x5());
	}

	private static function build(array $rules): PhpCsFixerConfig {
		return (new PhpCsFixerConfig)
			->setUsingCache(true)
			->setIndent("\t")
			->setLineEnding("\n")
			->setRiskyAllowed(true)
			->setRules($rules);
	}

}
