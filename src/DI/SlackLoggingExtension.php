<?php declare(strict_types = 1);

namespace Contributte\Logging\DI;

use Contributte\Logging\Slack\Formatter\ColorFormatter;
use Contributte\Logging\Slack\Formatter\ContextFormatter;
use Contributte\Logging\Slack\Formatter\ExceptionFormatter;
use Contributte\Logging\Slack\Formatter\ExceptionPreviousExceptionsFormatter;
use Contributte\Logging\Slack\Formatter\ExceptionStackTraceFormatter;
use Contributte\Logging\Slack\SlackLogger;
use Contributte\Logging\UniversalLogger;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\ServiceCreationException;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;

/**
 * @property-read stdClass $config
 */
final class SlackLoggingExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'url' => Expect::string()->required(),
			'channel' => Expect::string()->required(),
			'username' => Expect::string('Tracy'),
			'icon_emoji' => Expect::string(':rocket:'),
			'icon_url' => Expect::string()->nullable(),
			'formatters' => Expect::listOf('array|string|Nette\DI\Definitions\Statement')->default([
				ContextFormatter::class,
				ColorFormatter::class,
				ExceptionFormatter::class,
				ExceptionStackTraceFormatter::class,
				ExceptionPreviousExceptionsFormatter::class,
			]),
		]);
	}

	/**
	 * Register services
	 */
	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->config;

		$loggerSlack = $builder->addDefinition($this->prefix('logger'))
			->setFactory(SlackLogger::class, [(array) $config]);

		foreach ($config->formatters as $n => $formatter) {
			$def = $builder->addDefinition($this->prefix('formatter.' . ($n + 1)))
				->setType($formatter);

			$loggerSlack->addSetup('addFormatter', [$def]);
		}
	}

	/**
	 * Decorate services
	 */
	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		$logger = $builder->getByType(UniversalLogger::class);

		if ($logger === null) {
			throw new ServiceCreationException(
				sprintf(
					'Service "%s" is required. Did you register %s extension as well?',
					UniversalLogger::class,
					TracyLoggingExtension::class
				)
			);
		}

		$def = $builder->getDefinition($logger);
		assert($def instanceof ServiceDefinition);
		$def->addSetup('addLogger', ['@' . $this->prefix('logger')]);
	}

}
