<?php

namespace Contributte\Logging\Mailer;

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
