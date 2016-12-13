<?php

/**
 * TEST: Tracy\Logger\TracyFileLogger
 */

use Contributte\Logging\Tracy\Logger\TracyFileLogger;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

test(function () {
    Assert::false(file_exists(TEMP_DIR . '/critical.log'));
    $exception = new RuntimeException('Foobar', 100);

    $logger = new TracyFileLogger(TEMP_DIR);
    $logger->log($exception, $logger::CRITICAL);
    Assert::true(file_exists(TEMP_DIR . '/critical.log'));
});
