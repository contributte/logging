<?php declare(strict_types = 1);

namespace Contributte\Logging\Mailer;

interface IMailer
{

	public function send(mixed $message): void;

}
