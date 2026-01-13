<?php declare(strict_types = 1);

namespace Tests\Cases\Logger;

use Contributte\Logging\SendMailLogger;
use Contributte\Tester\Environment;
use Contributte\Tester\Toolkit;
use RuntimeException;
use Tester\Assert;
use Tests\Helpers\TestMailer;

require_once __DIR__ . '/../../bootstrap.php';

Toolkit::test(function (): void {
	// mailer should be called only once for default emailSnooze
	@unlink(Environment::getTestDir() . '/email-sent');
	$exception = new RuntimeException('Foobar', 100);
	$mailer = new TestMailer();
	$logger = new SendMailLogger($mailer, Environment::getTestDir());

	$logger->log($exception, $logger::CRITICAL);
	$logger->log($exception, $logger::CRITICAL);

	Assert::count(1, $mailer->messages);
});

Toolkit::test(function (): void {
	// mailer should be called multiple times for negative emailSnooze
	@unlink(Environment::getTestDir() . '/email-sent');
	$exception = new RuntimeException('Foobar', 100);
	$mailer = new TestMailer();
	$logger = new SendMailLogger($mailer, Environment::getTestDir());
	$logger->setEmailSnooze('-1');

	$logger->log($exception, $logger::CRITICAL);
	$logger->log($exception, $logger::CRITICAL);

	Assert::count(2, $mailer->messages);
});
