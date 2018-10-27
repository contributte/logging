<?php declare(strict_types = 1);

namespace Contributte\Logging\Mailer;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
interface IMailer
{

	/**
	 * @param mixed $message
	 */
	public function send($message): void;

}
