<?php declare(strict_types = 1);

/**
 * TEST: Logger\ExceptionFileLogger
 */

use Contributte\Logging\FileLogger;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

test(function (): void {
	Assert::false(file_exists(TEMP_DIR . '/critical.log'));
	$exception = new RuntimeException('Foobar', 100);

	$logger = new FileLogger(TEMP_DIR);
	$logger->log($exception, $logger::CRITICAL);
	Assert::true(file_exists(TEMP_DIR . '/critical.log'));
});
