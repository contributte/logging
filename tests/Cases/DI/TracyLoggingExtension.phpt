<?php declare(strict_types = 1);

namespace Tests\Cases\DI;

use Contributte\Logging\DI\TracyLoggingExtension;
use Contributte\Logging\UniversalLogger;
use Contributte\Tester\Toolkit;
use Contributte\Tester\Utils\ContainerBuilder;
use Nette\DI\InvalidConfigurationException;
use Tester\Assert;
use Tester\FileMock;
use Tracy\Bridges\Nette\TracyExtension;

require_once __DIR__ . '/../../bootstrap.php';

Toolkit::test(function (): void {
	Assert::exception(function (): void {
		ContainerBuilder::of()
			->withCompiler(function ($compiler): void {
				$compiler->addExtension('logging', new TracyLoggingExtension());
				$compiler->addExtension('tracy', new TracyExtension());
			})
			->build();
	}, InvalidConfigurationException::class, '~logging.*logDir~u');
});

Toolkit::test(function (): void {
	$container = ContainerBuilder::of()
		->withCompiler(function ($compiler): void {
			$compiler->addExtension('logging', new TracyLoggingExtension());
			$compiler->addExtension('tracy', new TracyExtension());
			$compiler->loadConfig(FileMock::create('
			logging:
				logDir: some-temp-dir
', 'neon'));
		})
		->build();

	Assert::type(UniversalLogger::class, $container->getService('logging.logger'));
	Assert::type(UniversalLogger::class, $container->getService('tracy.logger'));
});
