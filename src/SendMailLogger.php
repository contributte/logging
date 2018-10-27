<?php declare(strict_types = 1);

namespace Contributte\Logging;

use Contributte\Logging\Mailer\IMailer;
use Exception;

class SendMailLogger extends AbstractLogger
{

	/** @var string */
	private $emailSnooze = '2 days';

	/** @var IMailer */
	private $mailer;

	public function __construct(IMailer $mailer, string $directory)
	{
		parent::__construct($directory);
		$this->mailer = $mailer;
	}

	public function setEmailSnooze(string $emailSnooze): void
	{
		$this->emailSnooze = $emailSnooze;
	}

	public function setMailer(IMailer $mailer): void
	{
		$this->mailer = $mailer;
	}

	/**
	 * @param mixed $message
	 * @param string $priority
	 */
	public function log($message, $priority): void
	{
		if (!in_array($priority, [ILogger::ERROR, ILogger::EXCEPTION, ILogger::CRITICAL], true)) return;
		if (!($message instanceof Exception)) return;

		$snooze = is_numeric($this->emailSnooze)
			? $this->emailSnooze
			: @strtotime($this->emailSnooze) - time(); // @ timezone may not be set

		if (@filemtime($this->directory . '/email-sent') + $snooze < time() // @ file may not exist
			&& @file_put_contents($this->directory . '/email-sent', 'sent') // @ file may not be writable
		) {
			$this->mailer->send($message);
		}
	}

}
