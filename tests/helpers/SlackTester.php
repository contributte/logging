<?php

use Contributte\Logging\Exceptions\Logical\InvalidStateException;
use Contributte\Logging\Tracy\Logger\Slack\ColorFormatter;
use Contributte\Logging\Tracy\Logger\Slack\ContextFormatter;
use Contributte\Logging\Tracy\Logger\Slack\ExceptionFormatter;
use Contributte\Logging\Tracy\Logger\Slack\ExceptionPreviousExceptionsFormatter;
use Contributte\Logging\Tracy\Logger\Slack\ExceptionStackTraceFormatter;
use Contributte\Logging\Tracy\Logger\SlackLogger;

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
