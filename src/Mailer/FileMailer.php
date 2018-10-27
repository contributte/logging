<?php declare(strict_types = 1);

namespace Contributte\Logging\Mailer;

use Contributte\Logging\AbstractLogger;
use Exception;
use Tracy\Helpers;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
class FileMailer implements IMailer
{

	/** @var string */
	private $directory;

	public function __construct(string $directory)
	{
		$this->directory = $directory;
	}

	/**
	 * @param string|Exception $message
	 */
	public function send($message): void
	{
		$host = preg_replace('#[^\w.-]+#', '', $_SERVER['HTTP_HOST'] ?? php_uname('n'));
		$parts = str_replace(
			["\r\n", "\n"],
			["\n", PHP_EOL],
			[
				'headers' => implode("\n", [
						'From: file@mailer',
						'X-Mailer: Tracy',
						'Content-Type: text/plain; charset=UTF-8',
						'Content-Transfer-Encoding: 8bit',
					]) . "\n",
				'subject' => 'PHP: An error occurred on the server ' . $host,
				'body' => AbstractLogger::formatMessage($message) . "\n\nsource: " . Helpers::getSource(),
			]
		);

		@file_put_contents($this->directory . '/tracy-mail-' . time() . '.txt', implode("\n\n", $parts));
	}

}
