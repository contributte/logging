<?php declare(strict_types = 1);

namespace Contributte\Logging;

use Contributte\Logging\Exceptions\Logical\InvalidStateException;
use Exception;
use Tracy\Logger;

/**
 * ExceptionFileLogger based on official Tracy\Logger (@copyright David Grudl)
 *
 * Log all exceptions to exception.log
 *
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
class ExceptionFileLogger extends AbstractLogger implements ILogger
{

	/**
	 * @param string|Exception $message
	 * @param string $priority
	 */
	public function log($message, $priority): void
	{
		if (!is_dir($this->directory)) {
			throw new InvalidStateException('Directory "' . $this->directory . '" is not found or is not directory.');
		}

		$exceptionFile = ($message instanceof Exception) ? $this->getExceptionFile($message) : NULL;
		$line = Logger::formatLogLine($message, $exceptionFile);
		$file = $this->directory . '/' . strtolower($priority) . '.log';

		if (!@file_put_contents($file, $line . PHP_EOL, FILE_APPEND | LOCK_EX)) {
			throw new InvalidStateException('Unable to write to log file "' . $file . '". Is directory writable?');
		}
	}

}
