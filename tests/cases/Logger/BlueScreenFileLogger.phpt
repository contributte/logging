<?php declare(strict_types = 1);

/**
 * TEST: Logger\BlueScreenFileLogger
 */

use Contributte\Logging\BlueScreenFileLogger;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

test(function (): void {
	$exception = new RuntimeException('Foobar', 100);

	$logger = new BlueScreenFileLogger(TEMP_DIR);
	$logger->log($exception, $logger::CRITICAL);
	Assert::equal(1, count(glob(TEMP_DIR . '/exception*.html')));
});
