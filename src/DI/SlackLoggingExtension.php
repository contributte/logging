<?php declare(strict_types = 1);

namespace Contributte\Logging\DI;

use Contributte\Logging\DI\Configuration\SlackConfiguration;
use Contributte\Logging\Slack\Formatter\IFormatter;
use Contributte\Logging\Slack\SlackLogger;
use Contributte\Logging\UniversalLogger;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\ServiceCreationException;
use Nette\Utils\Validators;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 * @property SlackConfiguration $config
 */
final class SlackLoggingExtension extends CompilerExtension
{

	public function __construct()
	{
		$this->config = new SlackConfiguration();
	}

	/**
	 * Register services
	 */
	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('logger'))
			->setFactory(SlackLogger::class, [$this->config]);

		foreach ($this->config->formatters as $n => $formatter) {
			Validators::is($formatter, IFormatter::class);

			$builder->addDefinition($this->prefix('formatter.' . ($n + 1)))
				->setType($formatter);
		}
	}

	/**
	 * Decorate services
	 */
	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		$universalLogger = $builder->getByType(UniversalLogger::class);
		if ($universalLogger === null) {
			throw new ServiceCreationException(
				sprintf(
					'Service "%s" is required. Did you register %s extension as well?',
					UniversalLogger::class,
					TracyLoggingExtension::class
				)
			);
		}

		$universalLoggerDef = $builder->getDefinition($universalLogger);
		assert($universalLoggerDef instanceof ServiceDefinition);

		$loggerDef = $builder->getDefinition($this->prefix('logger'));
		assert($loggerDef instanceof ServiceDefinition);

		$universalLoggerDef->addSetup('addLogger', [$loggerDef]);

		foreach ($builder->findByType(IFormatter::class) as $formatter) {
			$loggerDef->addSetup('addFormatter', [$formatter]);
		}
	}

}
