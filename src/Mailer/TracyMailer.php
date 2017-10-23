<?php

namespace Contributte\Logging\Mailer;

use Contributte\Logging\Utils\Utils;
use Exception;
use Tracy\Helpers;

/**
 * TracyMailer based on official Tracy\Logger[default mailer] (@copyright David Grudl)
 *
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
class TracyMailer implements IMailer
{

	/** @var string */
	private $from;

	/** @var array */
	private $to = [];

	/**
	 * @param string $from
	 * @param array $to
	 */
	public function __construct($from = NULL, array $to)
	{
		$this->from = $from;
		$this->to = $to;
	}

	/**
	 * @param string|Exception $message
	 * @return void
	 */
	public function send($message)
	{
		$host = preg_replace('#[^\w.-]+#', '', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : php_uname('n'));
		$parts = str_replace(
			["\r\n", "\n"],
			["\n", PHP_EOL],
			[
				'headers' => implode("\n", [
						'From: ' . ($this->from ?: 'noreply@' . $host),
						'X-Mailer: Tracy',
						'Content-Type: text/plain; charset=UTF-8',
						'Content-Transfer-Encoding: 8bit',
					]) . "\n",
				'subject' => 'PHP: An error occurred on the server ' . $host,
				'body' => Utils::formatMessage($message) . "\n\nsource: " . Helpers::getSource(),
			]
		);

		$email = implode(',', $this->to);
		mail($email, $parts['subject'], $parts['body'], $parts['headers']);
	}

}
