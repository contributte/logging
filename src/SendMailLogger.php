<?php

namespace Contributte\Logging;

use Contributte\Logging\Mailer\IMailer;
use Throwable;

class SendMailLogger extends AbstractLogger
{

	/** @var string */
	private $emailSnooze = '2 days';

	/** @var IMailer */
	private $mailer;

	/**
	 * @param IMailer $mailer
	 * @param string $directory
	 */
	public function __construct(IMailer $mailer, $directory)
	{
		parent::__construct($directory);
		$this->mailer = $mailer;
	}

	/**
	 * @param string $emailSnooze
	 * @return void
	 */
	public function setEmailSnooze($emailSnooze)
	{
		$this->emailSnooze = $emailSnooze;
	}

	/**
	 * @param IMailer $mailer
	 * @return void
	 */
	public function setMailer($mailer)
	{
		$this->mailer = $mailer;
	}

	/**
	 * @param mixed $message
	 * @param string $priority
	 * @return void
	 */
	public function log($message, $priority)
	{
		if (!in_array($priority, [ILogger::ERROR, ILogger::EXCEPTION, ILogger::CRITICAL], TRUE))
			return;
		if (!($message instanceof Throwable))
			return;

		$snooze = is_numeric($this->emailSnooze)
			? $this->emailSnooze
			: @strtotime($this->emailSnooze) - time(); // @ timezone may not be set

		if ($this->mailer
			&& @filemtime($this->directory . '/email-sent') + $snooze < time() // @ file may not exist
			&& @file_put_contents($this->directory . '/email-sent', 'sent') // @ file may not be writable
		) {
			$this->mailer->send($message);
		}
	}

}
