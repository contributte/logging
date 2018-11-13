<?php declare(strict_types = 1);

namespace Contributte\Logging;

use Contributte\Logging\Exceptions\Logical\InvalidStateException;
use Throwable;
use Tracy\Logger;

/**
 * FileLogger based on official Tracy\Logger (@copyright David Grudl)
 *
 * Log all messages to <priority>.log
 *
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
class FileLogger extends AbstractLogger implements ILogger
{

	/**
	 * @param mixed $message
	 */
	public function log($message, string $priority = ILogger::INFO): void
	{
		if (!is_dir($this->directory)) {
			throw new InvalidStateException('Directory "' . $this->directory . '" is not found or is not directory.');
		}

		$exceptionFile = ($message instanceof Throwable) ? $this->getExceptionFile($message) : null;
		$line = Logger::formatLogLine($message, $exceptionFile);
		$file = $this->directory . '/' . strtolower($priority) . '.log';

		if (!(bool) @file_put_contents($file, $line . PHP_EOL, FILE_APPEND | LOCK_EX)) {
			throw new InvalidStateException('Unable to write to log file "' . $file . '". Is directory writable?');
		}
	}

}
