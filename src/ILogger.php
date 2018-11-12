<?php declare(strict_types = 1);

namespace Contributte\Logging;

use Tracy\ILogger as TracyLogger;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
interface ILogger
{

	public const DEBUG = TracyLogger::DEBUG;
	public const INFO = TracyLogger::INFO;
	public const WARNING = TracyLogger::WARNING;
	public const ERROR = TracyLogger::ERROR;
	public const EXCEPTION = TracyLogger::EXCEPTION;
	public const CRITICAL = TracyLogger::CRITICAL;

	/**
	 * @param mixed $message
	 */
	public function log($message, string $priority = ILogger::INFO): void;

}
