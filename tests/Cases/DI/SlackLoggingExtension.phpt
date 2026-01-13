<?php declare(strict_types = 1);

namespace Tests\Cases\DI;

use Contributte\Logging\DI\SlackLoggingExtension;
use Contributte\Logging\DI\TracyLoggingExtension;
use Contributte\Logging\Slack\SlackLogger;
use Contributte\Logging\UniversalLogger;
use Contributte\Tester\Environment;
use Contributte\Tester\Toolkit;
use Contributte\Tester\Utils\ContainerBuilder;
use Tester\Assert;
use Tester\FileMock;

require_once __DIR__ . '/../../bootstrap.php';

Toolkit::test(function (): void {
	$container = ContainerBuilder::of()
		->withCompiler(function ($compiler): void {
			$compiler->addExtension('logging', new TracyLoggingExtension());
			$compiler->addExtension('logging2slack', new SlackLoggingExtension());
			$compiler->loadConfig(FileMock::create('
			logging:
				logDir: %logDir%

			logging2slack:
				url: foobar.com
				channel: baz
', 'neon'));
			$compiler->addConfig(['parameters' => ['logDir' => Environment::getTestDir()]]);
		})
		->build();

	Assert::type(UniversalLogger::class, $container->getService('logging.logger'));
	Assert::type(SlackLogger::class, $container->getService('logging2slack.logger'));
});
