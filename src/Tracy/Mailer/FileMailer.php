<?php

namespace Contributte\Logging\Tracy\Mailer;

use Contributte\Logging\Tracy\Logger\AbstractTracyLogger;
use Exception;
use Throwable;
use Tracy\Helpers;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
final class FileMailer implements IMailer
{

	/** @var string */
	private $directory;

	/**
	 * @param string $directory
	 */
	public function __construct($directory)
	{
		$this->directory = $directory;
	}

	/**
	 * @param string|Exception|Throwable $message
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
						'From: file@mailer',
						'X-Mailer: Tracy',
						'Content-Type: text/plain; charset=UTF-8',
						'Content-Transfer-Encoding: 8bit',
					]) . "\n",
				'subject' => 'PHP: An error occurred on the server ' . $host,
				'body' => AbstractTracyLogger::formatMessage($message) . "\n\nsource: " . Helpers::getSource(),
			]
		);

		@file_put_contents($this->directory . '/tracy-mail-' . time() . '.txt', implode("\n\n", $parts));
	}

}
