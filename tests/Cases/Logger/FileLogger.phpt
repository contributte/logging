<?php declare(strict_types = 1);

namespace Tests\Cases\Logger;

use Contributte\Logging\FileLogger;
use Contributte\Tester\Environment;
use Contributte\Tester\Toolkit;
use RuntimeException;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

Toolkit::test(function (): void {
	Assert::false(file_exists(Environment::getTestDir() . '/critical.log'));
	$exception = new RuntimeException('Foobar', 100);

	$logger = new FileLogger(Environment::getTestDir());
	$logger->log($exception, $logger::CRITICAL);
	Assert::true(file_exists(Environment::getTestDir() . '/critical.log'));
});
