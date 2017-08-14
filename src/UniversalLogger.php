<?php

namespace Contributte\Logging;

use Contributte\Logging\Listener\IListener;
use Tracy\ILogger as TracyLogger;

class UniversalLogger implements TracyLogger
{

	/** @var ILogger[] */
	private $loggers = [];

	/** @var IListener[] */
	private $listeners = [];

	/**
	 * @param ILogger $logger
	 * @return void
	 */
	public function addLogger(ILogger $logger)
	{
		$this->loggers[] = $logger;
	}

	/**
	 * @param IListener $listener
	 * @return void
	 */
	public function addListener(IListener $listener)
	{
		$this->listeners[] = $listener;
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
		// Trigger listeners before
		foreach ($this->listeners as $listener) {
			$listener->beforeLog($message, $priority);
		}

		// Composite logger
		foreach ($this->loggers as $logger) {
			$logger->log($message, $priority);
		}

		// Trigger listeners after
		foreach ($this->listeners as $listener) {
			$listener->afterLog($message, $priority);
		}
	}

}
