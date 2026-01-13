<?php declare(strict_types = 1);

namespace Tests\Helpers;

use Contributte\Logging\Mailer\IMailer;

class TestMailer implements IMailer
{

	/** @var mixed[] */
	public array $messages = [];

	public function send(mixed $message): void
	{
		$this->messages[] = $message;
	}

}
