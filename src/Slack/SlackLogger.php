<?php

namespace Contributte\Logging\Slack;

use Contributte\Logging\Exceptions\Runtime\Logger\SlackBadRequestException;
use Contributte\Logging\ILogger;
use Contributte\Logging\Slack\Formatter\IFormatter;
use Contributte\Logging\Slack\Formatter\SlackContext;
use Exception;
use Nette\Utils\Arrays;
use Throwable;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
final class SlackLogger implements ILogger
{

	/** @var array */
	private $config;

	/** @var IFormatter[] */
	private $formatters = [];

	/**
	 * @param array $config
	 */
	public function __construct(array $config)
	{
		$this->config = $config;
	}

	/**
	 * @param IFormatter $formatter
	 * @return void
	 */
	public function addFormatter(IFormatter $formatter)
	{
		$this->formatters[] = $formatter;
	}

	/**
	 * @param string|Exception|Throwable $message
	 * @param string $priority
	 * @return void
	 */
	public function log($message, $priority)
	{
		if (!in_array($priority, [ILogger::ERROR, ILogger::EXCEPTION, ILogger::CRITICAL], TRUE)) return;
		if (!($message instanceof Exception) || !($message instanceof Throwable)) return;

		$context = new SlackContext($this->config);

		// Apply all formatters
		foreach ($this->formatters as $formatter) {
			$context = $formatter->format($context, $message, $priority);
		}

		// Send to channel
		$this->makeRequest($context);
	}

	/**
	 * @param SlackContext $context
	 * @return void
	 */
	protected function makeRequest(SlackContext $context)
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

		$response = @file_get_contents($url, NULL, stream_context_create($streamcontext));

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
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	protected function get($key, $default = NULL)
	{
		if (func_num_args() > 1) {
			$value = Arrays::get($this->config, explode('.', $key), $default);
		} else {
			$value = Arrays::get($this->config, explode('.', $key));
		}

		return $value;
	}

}
