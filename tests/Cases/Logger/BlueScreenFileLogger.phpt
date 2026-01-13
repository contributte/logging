<?php declare(strict_types = 1);

namespace Tests\Cases\Logger;

use Contributte\Logging\BlueScreenFileLogger;
use Contributte\Tester\Environment;
use Contributte\Tester\Toolkit;
use RuntimeException;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

Toolkit::test(function (): void {
	$exception = new RuntimeException('Foobar', 100);

	$logger = new BlueScreenFileLogger(Environment::getTestDir());
	$logger->log($exception, $logger::CRITICAL);
	Assert::equal(1, count(glob(Environment::getTestDir() . '/exception*.html')));
});
