<?php declare(strict_types = 1);

namespace Contributte\Logging\DI;

use Contributte\Logging\Sentry\SentryLogger;
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
final class SentryLoggingExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'url' => Expect::string()->required(),
			'enabled' => Expect::bool(true),
			'options' => Expect::array(),
		]);
	}

	/**
	 * Register services
	 */
	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->config;
		if ($config->enabled === false) return;

		$builder->addDefinition($this->prefix('logger'))
			->setFactory(SentryLogger::class, [(array) $config]);
	}

	/**
	 * Decorate services
	 */
	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->config;
		if ($config->enabled === false) return;

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
