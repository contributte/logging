<?php declare(strict_types = 1);

namespace Contributte\Logging\Sentry;

use Contributte\Logging\Exceptions\Logical\InvalidStateException;
use Contributte\Logging\ILogger;
use Raven_Client;

final class SentryLogger implements ILogger
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

	/** @var Raven_Client|null */
	private $client;

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

	private function getClient(): Raven_Client
	{
		// todo: delegate to user completely?
		if ($this->client === null) {
			$this->client = new Raven_Client(
				$this->configuration[self::CONFIG_URL],
				$this->configuration[self::CONFIG_OPTIONS]
			);
		}
		return $this->client;
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
		if (in_array($priority, $this->allowedPriority, true)
			&& ($message instanceof \Throwable)) {
			$this->sendError($message, [
				'level' => $this->mapLevel($priority),
			]);
			return;
		}

		// all other messages:
		// todo: does this really make sense? Shouldn't be added everything to breadcrumbs and errors handled separately?
		// todo: this would probably make more sense, as breacrumbs are sent nowhere when no error happens
		// todo: and it makes send to have also things from not allowed levels, as only last 100 breadcrums are associated with an error
		$this->getClient()->breadcrumbs->record([
			'message' => (string) $message,
			'category' => 'logged-message',
			'level' => $this->mapLevel($priority),
		]);
	}

	/**
	 * @param mixed $message
	 * @param mixed[] $data
	 */
	protected function sendError($message, array $data): void
	{
		if ($message instanceof \Throwable) {
			$this->getClient()->captureException($message, $data);
		} else {
			$this->getClient()->captureMessage($message, [], $data);
		}
	}

	private function mapLevel(string $priority): string
	{
		return self::LEVEL_PRIORITY_MAP[$priority] ?? Raven_Client::INFO;
	}

}
