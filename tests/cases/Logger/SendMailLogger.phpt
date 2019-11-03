<?php declare(strict_types = 1);

/**
 * TEST: Logger\SendMailLogger
 */

use Contributte\Logging\SendMailLogger;
use Tester\Assert;
use Tests\Helpers\TestMailer;

require_once __DIR__ . '/../../bootstrap.php';

test(function (): void {
	// mailer should be called only once for default emailSnooze
	@unlink(TEMP_DIR . '/email-sent');
	$exception = new RuntimeException('Foobar', 100);
	$mailer = new TestMailer();
	$logger = new SendMailLogger($mailer, TEMP_DIR);

	$logger->log($exception, $logger::CRITICAL);
	$logger->log($exception, $logger::CRITICAL);

	Assert::count(1, $mailer->messages);
});

test(function (): void {
	// mailer should be called multiple times for negative emailSnooze
	@unlink(TEMP_DIR . '/email-sent');
	$exception = new RuntimeException('Foobar', 100);
	$mailer = new TestMailer();
	$logger = new SendMailLogger($mailer, TEMP_DIR);
	$logger->setEmailSnooze('-1');

	$logger->log($exception, $logger::CRITICAL);
	$logger->log($exception, $logger::CRITICAL);

	Assert::count(2, $mailer->messages);
});
