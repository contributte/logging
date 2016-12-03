<?php

namespace Contributte\Logging\Tracy\Mailer;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
interface IMailer
{

	/**
	 * @param mixed $message
	 * @return void
	 */
	public function send($message);

}
