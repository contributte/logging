<?php

/**
 * TEST: DI\SlackLoggingExtension */

use Contributte\Logging\DI\SlackLoggingExtension;
use Contributte\Logging\DI\TracyLoggingExtension;
use Contributte\Logging\Slack\SlackLogger;
use Contributte\Logging\UniversalLogger;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Tester\Assert;
use Tester\FileMock;

require_once __DIR__ . '/../../bootstrap.php';

test(function () {
	$loader = new ContainerLoader(TEMP_DIR, TRUE);
	$class = $loader->load(function (Compiler $compiler) {
		$compiler->addExtension('logging', new TracyLoggingExtension());
		$compiler->addExtension('logging2slack', new SlackLoggingExtension());
		$compiler->loadConfig(FileMock::create('
		logging:
			logDir: %logDir%

		logging2slack:
			url: foobar.com
			channel: baz
', 'neon'));
		$compiler->addConfig(['parameters' => ['logDir' => TMP_DIR]]);
	}, 1);

	/** @var Container $container */
	$container = new $class;

	Assert::type(UniversalLogger::class, $container->getService('logging.logger'));
	Assert::type(SlackLogger::class, $container->getService('logging2slack.logger'));
});
