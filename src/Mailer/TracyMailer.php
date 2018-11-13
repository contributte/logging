<?php declare(strict_types = 1);

namespace Contributte\Logging\Mailer;

use Tracy\Helpers;
use Tracy\Logger;

/**
 * TracyMailer based on official Tracy\Logger[default mailer] (@copyright David Grudl)
 *
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
class TracyMailer implements IMailer
{

	/** @var string|null */
	private $from;

	/** @var mixed[] */
	private $to = [];

	/**
	 * @param mixed[] $to
	 */
	public function __construct(?string $from = null, array $to)
	{
		$this->from = $from;
		$this->to = $to;
	}

	/**
	 * @param mixed $message
	 */
	public function send($message): void
	{
		/** @var string $host */
		$host = preg_replace('#[^\w.-]+#', '', $_SERVER['HTTP_HOST'] ?? php_uname('n'));
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
				'body' => Logger::formatMessage($message) . "\n\nsource: " . Helpers::getSource(),
			]
		);

		$email = implode(',', $this->to);
		mail($email, $parts['subject'], $parts['body'], $parts['headers']);
	}

}
