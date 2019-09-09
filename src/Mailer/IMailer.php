<?php declare(strict_types = 1);

namespace Contributte\Logging\Mailer;

interface IMailer
{

	/**
	 * @param mixed $message
	 */
	public function send($message): void;

}
