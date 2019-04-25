<?php declare(strict_types = 1);

namespace Contributte\Logging\DI;

use Contributte\Logging\DI\Configuration\SentryConfiguration;
use Contributte\Logging\Sentry\SentryLogger;
use Contributte\Logging\UniversalLogger;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\ServiceCreationException;

/**
 * @property SentryConfiguration $config
 */
final class SentryLoggingExtension extends CompilerExtension
{

	public function __construct()
	{
		$this->config = new SentryConfiguration();
	}

	/**
	 * Register services
	 */
	public function loadConfiguration(): void
	{
		if (!$this->config->enabled) {
			return;
		}

		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('logger'))
			->setFactory(SentryLogger::class, [$this->config]);
	}

	/**
	 * Decorate services
	 */
	public function beforeCompile(): void
	{
		if (!$this->config->enabled) {
			return;
		}

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
