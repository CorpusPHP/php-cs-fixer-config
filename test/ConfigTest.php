<?php

namespace Corpus\PhpCsFixer\Test;

use Corpus\PhpCsFixer\Config;
use PhpCsFixer\Config as PhpCsFixerConfig;
use PhpCsFixer\RuleSet\RuleSet;
use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase {

	public function testPhp7x4ReturnsConfiguredPhpCsFixerConfig(): void {
		$config = Config::php7x4();

		$this->assertInstanceOf(PhpCsFixerConfig::class, $config);
		$this->assertTrue($config->getRiskyAllowed());
		$this->assertTrue($config->getUsingCache());
		$this->assertSame("\t", $config->getIndent());
		$this->assertSame("\n", $config->getLineEnding());
		$this->assertSame(true, $config->getRules()['@PHP7x4Migration']);
		$this->assertSame(['syntax' => 'short'], $config->getRules()['array_syntax']);
		$this->assertSame(
			[
				'null_adjustment' => 'always_last',
				'sort_algorithm' => 'alpha',
			],
			$config->getRules()['phpdoc_types_order'],
		);
	}

	public function testPhp7x4DoesNotEnablePhp8Rules(): void {
		$rules = Config::php7x4()->getRules();

		$this->assertSame(true, $rules['@PHP7x4Migration']);
		$this->assertArrayNotHasKey('@PHP8x1Migration', $rules);
		$this->assertArrayNotHasKey('php_unit_attributes', $rules);
		$this->assertArrayNotHasKey('stringable_for_to_string', $rules);
		$this->assertSame(
			['arrays', 'arguments'],
			$rules['trailing_comma_in_multiline']['elements'],
		);
	}

	public function testPhp8x1AddsPhp8Rules(): void {
		$rules = Config::php8x1()->getRules();

		$this->assertSame(true, $rules['@PHP8x1Migration']);
		$this->assertArrayNotHasKey('@PHP7x4Migration', $rules);
		$this->assertSame(true, $rules['modernize_strpos']);
		$this->assertSame(true, $rules['php_unit_attributes']);
		$this->assertSame(true, $rules['stringable_for_to_string']);
		$this->assertSame(
			['arrays', 'arguments', 'parameters', 'match'],
			$rules['trailing_comma_in_multiline']['elements'],
		);
	}

	public function testPhp8x2AddsReadonlyPropertyRule(): void {
		$rules = Config::php8x2()->getRules();

		$this->assertSame(true, $rules['@PHP8x2Migration']);
		$this->assertArrayNotHasKey('@PHP8x1Migration', $rules);
		$this->assertSame(true, $rules['php_unit_attributes']);
		$this->assertArrayNotHasKey('@PHPUnit11x0Migration:risky', $rules);
		$this->assertSame(true, $rules['no_redundant_readonly_property']);
	}

	public function testPhp8x5DoesNotEnablePhpUnit11Migration(): void {
		$rules = Config::php8x5()->getRules();

		$this->assertSame(true, $rules['@PHP8x5Migration']);
		$this->assertArrayNotHasKey('@PHP8x2Migration', $rules);
		$this->assertArrayNotHasKey('@PHPUnit11x0Migration:risky', $rules);
	}

	public function testAllRuleSetsResolve(): void {
		foreach ([Config::php7x4(), Config::php8x1(), Config::php8x2(), Config::php8x5()] as $config) {
			$this->assertNotEmpty((new RuleSet($config->getRules()))->getRules());
		}
	}

	public function testPhpFactoriesHaveOneMigrationRuleSet(): void {
		$factories = [];

		foreach ((new \ReflectionClass(Config::class))->getMethods() as $method) {
			if (!$method->isPublic() || !$method->isStatic() || strpos($method->getName(), 'php') !== 0) {
				continue;
			}

			$factories[] = $method->getName();
		}

		$this->assertNotEmpty($factories);

		foreach ($factories as $factory) {
			$migrations = array_filter(
				array_keys(Config::{$factory}()->getRules()),
				static function (string $rule): bool {
					return preg_match('/^@PHP\d+x\d+Migration$/', $rule) === 1;
				},
			);

			$this->assertCount(1, $migrations, $factory);
		}
	}

	public function testFactoriesReturnIndependentConfigurations(): void {
		$config = Config::php7x4();
		$config->setRules([]);

		$this->assertSame(true, Config::php7x4()->getRules()['@PHP7x4Migration']);
	}

}
