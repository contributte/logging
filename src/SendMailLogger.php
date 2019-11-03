<?php declare(strict_types = 1);

namespace Contributte\Logging;

use Contributte\Logging\Mailer\IMailer;
use Nette\InvalidArgumentException;

class SendMailLogger extends AbstractLogger
{

	/** @var string */
	private $emailSnooze = '2 days';

	/** @var IMailer */
	private $mailer;

	/** @var string[] */
	private $allowedPriority = [ILogger::ERROR, ILogger::EXCEPTION, ILogger::CRITICAL];

	public function __construct(IMailer $mailer, string $directory)
	{
		parent::__construct($directory);
		$this->mailer = $mailer;
	}

	/**
	 * @param string[] $allowedPriority
	 */
	public function setAllowedPriority(array $allowedPriority): void
	{
		$this->allowedPriority = $allowedPriority;
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
	 */
	public function log($message, string $priority = ILogger::INFO): void
	{
		if (!in_array($priority, $this->allowedPriority, true)) {
			return;
		}

		if (is_numeric($this->emailSnooze)) {
			$snooze = (int) $this->emailSnooze;
		} else {
			$strtotime = @strtotime($this->emailSnooze);

			if ($strtotime === false) {
				throw new InvalidArgumentException('Email snooze was not parsed');
			}

			$snooze = $strtotime - time();
		}

		$filemtime = @filemtime($this->directory . '/email-sent');

		if ($filemtime === false) {
			$filemtime = 0;
		}

		if ($filemtime + $snooze < time() && (bool) @file_put_contents($this->directory . '/email-sent', 'sent')
		) {
			$this->mailer->send($message);
		}
	}

}
