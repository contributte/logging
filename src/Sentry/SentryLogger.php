<?php declare(strict_types = 1);

namespace Contributte\Logging\Sentry;

use Contributte\Logging\Exceptions\Logical\InvalidStateException;
use Contributte\Logging\ILogger;
use Raven_Client;
use Throwable;

class SentryLogger implements ILogger
{

	public const LEVEL_PRIORITY_MAP = [
		self::DEBUG => Raven_Client::DEBUG,
		self::INFO => Raven_Client::INFO,
		self::WARNING => Raven_Client::WARNING,
		self::ERROR => Raven_Client::ERROR,
		self::EXCEPTION => Raven_Client::FATAL,
		self::CRITICAL => Raven_Client::FATAL,
	];

	public const CONFIG_URL = 'url';
	public const CONFIG_OPTIONS = 'options';

	/** @var mixed[] */
	protected $configuration;

	/** @var string[] */
	private $allowedPriority = [ILogger::ERROR, ILogger::EXCEPTION, ILogger::CRITICAL];

	/**
	 * @param mixed[] $configuration
	 */
	public function __construct(array $configuration)
	{
		if (!isset($configuration[self::CONFIG_URL])) {
			throw new InvalidStateException('Missing url in SentryLogger configuration');
		}

		if (!isset($configuration[self::CONFIG_OPTIONS])) {
			$configuration[self::CONFIG_OPTIONS] = [];
		}

		$this->configuration = $configuration;
	}

	/**
	 * @param string[] $allowedPriority
	 */
	public function setAllowedPriority(array $allowedPriority): void
	{
		$this->allowedPriority = $allowedPriority;
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
	 * @param mixed $message
	 */
	public function log($message, string $priority = ILogger::INFO): void
	{
		if (!in_array($priority, $this->allowedPriority, true)) {
			return;
		}

		$level = $this->getLevel($priority);

		if ($level === null) {
			return;
		}

		$data = [
			'level' => $level,
		];

		$this->makeRequest($message, $data);
	}

	/**
	 * @param mixed $message
	 * @param mixed[] $data
	 */
	protected function makeRequest($message, array $data): void
	{
		$client = new Raven_Client(
			$this->configuration[self::CONFIG_URL],
			$this->configuration[self::CONFIG_OPTIONS]
		);

		if ($message instanceof Throwable) {
			$client->captureException($message, $data);
		} else {
			$client->captureMessage($message, [], $data);
		}
	}

	protected function getLevel(string $priority): ?string
	{
		return self::LEVEL_PRIORITY_MAP[$priority] ?? null;
	}

}
