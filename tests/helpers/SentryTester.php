<?php

use Contributte\Logging\Exceptions\Logical\InvalidStateException;
use Contributte\Logging\Sentry\SentryLogger;

require_once __DIR__ . '/../../vendor/autoload.php';

$logger = new SentryLogger([
	'url' => '__URL__',
]);

$previous = new RuntimeException('Ooo', 100);
$exception = new InvalidStateException('Axx', 300, $previous);
$logger->log($exception, SentryLogger::CRITICAL);
