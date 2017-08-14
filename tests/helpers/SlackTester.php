<?php

use Contributte\Logging\Exceptions\Logical\InvalidStateException;
use Contributte\Logging\Slack\Formatter\ColorFormatter;
use Contributte\Logging\Slack\Formatter\ContextFormatter;
use Contributte\Logging\Slack\Formatter\ExceptionFormatter;
use Contributte\Logging\Slack\Formatter\ExceptionPreviousExceptionsFormatter;
use Contributte\Logging\Slack\Formatter\ExceptionStackTraceFormatter;
use Contributte\Logging\Slack\SlackLogger;

require_once __DIR__ . '/../../vendor/autoload.php';

$logger = new SlackLogger([
	'url' => '__URL__',
	'channel' => '__CHANNEL__',
]);
$logger->addFormatter(new ContextFormatter());
$logger->addFormatter(new ColorFormatter());
$logger->addFormatter(new ExceptionFormatter());
$logger->addFormatter(new ExceptionPreviousExceptionsFormatter());
$logger->addFormatter(new ExceptionStackTraceFormatter());

$previous = new RuntimeException('Ooo', 100);
$exception = new InvalidStateException('Axx', 300, $previous);
$logger->log($exception, SlackLogger::CRITICAL);
