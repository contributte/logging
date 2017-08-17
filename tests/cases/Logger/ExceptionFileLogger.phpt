<?php

/**
 * TEST: Logger\ExceptionFileLogger
 */

use Contributte\Logging\ExceptionFileLogger;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

test(function () {
	Assert::false(file_exists(TEMP_DIR . '/critical.log'));
	$exception = new RuntimeException('Foobar', 100);

	$logger = new ExceptionFileLogger(TEMP_DIR);
	$logger->log($exception, $logger::CRITICAL);
	Assert::true(file_exists(TEMP_DIR . '/critical.log'));
});
