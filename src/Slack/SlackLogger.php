<?php declare(strict_types = 1);

namespace Contributte\Logging\Slack;

use Contributte\Logging\DI\Configuration\SlackConfiguration;
use Contributte\Logging\Exceptions\Runtime\Logger\SlackBadRequestException;
use Contributte\Logging\ILogger;
use Contributte\Logging\Slack\Formatter\IFormatter;
use Contributte\Logging\Slack\Formatter\SlackContext;
use Throwable;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
final class SlackLogger implements ILogger
{

	/** @var SlackConfiguration */
	private $config;

	/** @var IFormatter[] */
	private $formatters = [];

	public function __construct(SlackConfiguration $config)
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
		if (!in_array($priority, [ILogger::ERROR, ILogger::EXCEPTION, ILogger::CRITICAL], true)) return;
		if (!($message instanceof Throwable)) return;

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
		$url = $this->config->url;

		$streamcontext = [
			'http' => [
				'method' => 'POST',
				'header' => 'Content-type: application/x-www-form-urlencoded',
				'timeout' => $this->config->timeout ?? 30,
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

}
