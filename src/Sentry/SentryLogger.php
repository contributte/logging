<?php declare(strict_types = 1);

namespace Contributte\Logging\Sentry;

use Contributte\Logging\ILogger;
use Raven_Client;
use Throwable;

class SentryLogger implements ILogger
{

	/** @var mixed[] */
	private $config;

	/**
	 * @param mixed[] $config
	 */
	public function __construct(array $config)
	{
		$this->config = $config;
	}

	/**
	 * @param mixed $message
	 */
	public function log($message, string $priority = ILogger::INFO): void
	{
		if (!in_array($priority, [ILogger::ERROR, ILogger::EXCEPTION, ILogger::CRITICAL], true)) return;
		if (!($message instanceof Throwable)) return;

		// Send to Sentry
		$this->makeRequest($message);
	}

	protected function makeRequest(Throwable $message): void
	{
		$client = new Raven_Client($this->config['url']);
		$client->captureException($message);
	}

}
