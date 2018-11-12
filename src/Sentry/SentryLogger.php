<?php declare(strict_types = 1);

namespace Contributte\Logging\Sentry;

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

	/** @var string */
	private $url;

	/**
	 * @param string $url
	 */
	public function __construct(string $url)
	{
		$this->url = $url;
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
	 * @param mixed $message
	 */
	public function log($message, string $priority = ILogger::INFO): void
	{
		if (!in_array($priority, [ILogger::ERROR, ILogger::EXCEPTION, ILogger::CRITICAL], TRUE)) return;

		$level = $this->getLevel($priority);

		if ($level === NULL) return;

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
		$client = new Raven_Client($this->url);
		if ($message instanceof Throwable) {
			$client->captureException($message, $data);
		} else {
			$client->captureMessage($message, [], $data);
		}
	}

	/**
	 * @param string $priority
	 * @return string|null
	 */
	protected function getLevel(string $priority): ?string
	{
		return self::LEVEL_PRIORITY_MAP[$priority] ?? NULL;
	}

}
