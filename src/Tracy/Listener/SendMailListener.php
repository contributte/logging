<?php

namespace Contributte\Logging\Tracy\Listener;

use Contributte\Logging\Tracy\Logger\ILogger;
use Contributte\Logging\Tracy\Mailer\IMailer;
use Exception;
use Throwable;

final class SendMailListener extends AbstractListener
{

	/** @var string */
	private $emailSnooze = '2 days';

	/** @var string */
	private $directory;

	/** @var IMailer */
	private $mailer;

	/**
	 * @param string $directory
	 * @param IMailer $mailer
	 */
	public function __construct($directory, IMailer $mailer = NULL)
	{
		$this->directory = $directory;
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
	 * @param string $directory
	 * @return void
	 */
	public function setDirectory($directory)
	{
		$this->directory = $directory;
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
	public function afterLog($message, $priority)
	{
		if (!in_array($priority, [ILogger::ERROR, ILogger::EXCEPTION, ILogger::CRITICAL], TRUE)) return;
		if (!($message instanceof Exception) || !($message instanceof Throwable)) return;

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
