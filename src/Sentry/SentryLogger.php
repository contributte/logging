<?php

namespace Contributte\Logging\Sentry;

use Contributte\Logging\ILogger;
use Exception;
use Raven_Client;
use Throwable;

final class SentryLogger implements ILogger
{

	/** @var array */
	private $config;

	/**
	 * @param array $config
	 */
	public function __construct(array $config)
	{
		$this->config = $config;
	}

	/**
	 * @param string|Exception|Throwable $message
	 * @param string $priority
	 * @return void
	 */
	public function log($message, $priority)
	{
		if (!in_array($priority, [ILogger::ERROR, ILogger::EXCEPTION, ILogger::CRITICAL], TRUE))
			return;
		if (!($message instanceof Throwable))
			return;

		// Send to Sentry
		$this->makeRequest($message);
	}

	/**
	 * @param Throwable $message
	 * @return void
	 */
	protected function makeRequest(Throwable $message)
	{
		$client = new Raven_Client($this->config['url']);
		$client->captureException($message);
	}

}
