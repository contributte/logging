<?php

namespace Contributte\Logging;

use Tracy\ILogger as TracyLogger;

class UniversalLogger implements TracyLogger
{

	/** @var ILogger[] */
	private $loggers = [];

	/**
	 * @param ILogger $logger
	 * @return void
	 */
	public function addLogger(ILogger $logger)
	{
		$this->loggers[] = $logger;
	}


	/**
	 * LOGGER ******************************************************************
	 */

	/**
	 * @param mixed $message
	 * @param string $priority
	 * @return void
	 */
	public function log($message, $priority = self::INFO)
	{
		// Composite logger
		foreach ($this->loggers as $logger) {
			$logger->log($message, $priority);
		}
	}

}
