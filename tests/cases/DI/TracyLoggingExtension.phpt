<?php declare(strict_types = 1);

/**
 * TEST: DI\TracyLoggingExtension */

use Contributte\Logging\DI\TracyLoggingExtension;
use Contributte\Logging\UniversalLogger;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Nette\Utils\AssertionException;
use Tester\Assert;
use Tester\FileMock;
use Tracy\Bridges\Nette\TracyExtension;

require_once __DIR__ . '/../../bootstrap.php';

test(function (): void {
	Assert::exception(function (): void {
		$loader = new ContainerLoader(TEMP_DIR, true);
		$loader->load(function (Compiler $compiler): void {
			$compiler->addExtension('logging', new TracyLoggingExtension());
			$compiler->addExtension('tracy', new TracyExtension());
		}, 1);
	}, AssertionException::class, 'The logging directory (logDir) expects to be string, %a% given.');
});

test(function (): void {
	$loader = new ContainerLoader(TEMP_DIR, true);
	$class = $loader->load(function (Compiler $compiler): void {
		$compiler->addExtension('logging', new TracyLoggingExtension());
		$compiler->addExtension('tracy', new TracyExtension());
		$compiler->loadConfig(FileMock::create('
		logging:
			logDir: some-temp-dir
', 'neon'));
	}, 2);

	/** @var Container $container */
	$container = new $class();

	Assert::type(UniversalLogger::class, $container->getService('logging.logger'));
	Assert::type(UniversalLogger::class, $container->getService('tracy.logger'));
});
