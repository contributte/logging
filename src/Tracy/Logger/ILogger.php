<?php

namespace Contributte\Logging\Tracy\Logger;

use Tracy\ILogger as TracyLogger;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
interface ILogger
{

	const DEBUG = TracyLogger::DEBUG;
	const INFO = TracyLogger::INFO;
	const WARNING = TracyLogger::WARNING;
	const ERROR = TracyLogger::ERROR;
	const EXCEPTION = TracyLogger::EXCEPTION;
	const CRITICAL = TracyLogger::CRITICAL;

	/**
	 * @param mixed $message
	 * @param string $priority
	 * @return void
	 */
	public function log($message, $priority);

}
