<?php declare(strict_types = 1);

namespace Tests\Helpers;

use Contributte\Logging\Mailer\IMailer;

class TestMailer implements IMailer
{

	/** @var mixed[] */
	public $messages = [];

	/**
	 * @param mixed $message
	 */
	public function send($message): void
	{
		$this->messages[] = $message;
	}

}
