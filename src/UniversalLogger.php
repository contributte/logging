<?php declare(strict_types = 1);

namespace Contributte\Logging;

use Tracy\ILogger as TracyLogger;

class UniversalLogger implements TracyLogger
{

	/** @var ILogger[] */
	private $loggers = [];

	public function addLogger(ILogger $logger): void
	{
		$this->loggers[] = $logger;
	}


	/**
	 * LOGGER ******************************************************************
	 */

	/**
	 * @param mixed $message
	 * @param string $priority
	 */
	public function log($message, $priority = self::INFO): void // phpcs:ignore
	{
		// Composite logger
		foreach ($this->loggers as $logger) {
			$logger->log($message, $priority);
		}
	}

}
