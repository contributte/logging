<?php declare(strict_types = 1);

namespace Contributte\Logging\Slack;

use Contributte\Logging\Exceptions\Runtime\Logger\SlackBadRequestException;
use Contributte\Logging\ILogger;
use Contributte\Logging\Slack\Formatter\IFormatter;
use Contributte\Logging\Slack\Formatter\SlackContext;
use Exception;
use Nette\Utils\Arrays;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
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
	 * @param string|Exception $message
	 * @param string $priority
	 */
	public function log($message, $priority): void
	{
		if (!in_array($priority, [ILogger::ERROR, ILogger::EXCEPTION, ILogger::CRITICAL], true)) return;
		if (!($message instanceof Exception)) return;

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

		$response = @file_get_contents($url, null, stream_context_create($streamcontext));

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
		if (func_num_args() > 1) {
			$value = Arrays::get($this->config, explode('.', $key), $default);
		} else {
			$value = Arrays::get($this->config, explode('.', $key));
		}

		return $value;
	}

}
