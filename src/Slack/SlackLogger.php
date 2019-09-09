<?php declare(strict_types = 1);

namespace Contributte\Logging\Slack;

use Contributte\Logging\Exceptions\Runtime\Logger\SlackBadRequestException;
use Contributte\Logging\ILogger;
use Contributte\Logging\Slack\Formatter\IFormatter;
use Contributte\Logging\Slack\Formatter\SlackContext;
use Nette\Utils\Arrays;
use Throwable;

final class SlackLogger implements ILogger
{

	/** @var mixed[] */
	private $config;

	/** @var IFormatter[] */
	private $formatters = [];

	/**
	 * @param mixed[] $config
	 */
	public function __construct(array $config)
	{
		$this->config = $config;
	}

	public function addFormatter(IFormatter $formatter): void
	{
		$this->formatters[] = $formatter;
	}

	/**
	 * @param mixed $message
	 */
	public function log($message, string $priority = ILogger::INFO): void
	{
		if (!in_array($priority, [ILogger::ERROR, ILogger::EXCEPTION, ILogger::CRITICAL], true)) {
			return;
		}

		if (!($message instanceof Throwable)) {
			return;
		}

		$context = new SlackContext($this->config);

		// Apply all formatters
		foreach ($this->formatters as $formatter) {
			$context = $formatter->format($context, $message, $priority);
		}

		// Send to channel
		$this->makeRequest($context);
	}

	protected function makeRequest(SlackContext $context): void
	{
		$url = $this->get('url');

		$streamcontext = [
			'http' => [
				'method' => 'POST',
				'header' => 'Content-type: application/x-www-form-urlencoded',
				'timeout' => $this->get('timeout', 30),
				'content' => http_build_query([
					'payload' => json_encode(array_filter($context->toArray())),
				]),
			],
		];

		$response = @file_get_contents($url, false, stream_context_create($streamcontext));

		if ($response !== 'ok') {
			throw new SlackBadRequestException([
				'url' => $url,
				'context' => $streamcontext,
				'response' => [
					'headers' => $http_response_header,
				],
			]);
		}
	}

	/**
	 * @param mixed $default
	 * @return mixed
	 */
	protected function get(string $key, $default = null)
	{
		return func_num_args() > 1
			? Arrays::get($this->config, explode('.', $key), $default)
			: Arrays::get($this->config, explode('.', $key));
	}

}
