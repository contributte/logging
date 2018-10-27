<?php declare(strict_types = 1);

namespace Contributte\Logging\DI;

use Contributte\Logging\Sentry\SentryLogger;
use Contributte\Logging\UniversalLogger;
use Nette\DI\CompilerExtension;
use Nette\DI\ServiceCreationException;
use Nette\Utils\Validators;

final class SentryLoggingExtension extends CompilerExtension
{

	/** @var mixed[] */
	private $defaults = [
		'url' => null,
		'enabled' => true,
	];

	/**
	 * Register services
	 */
	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults);
		if ($config['enabled'] === false) return;

		Validators::assertField($config, 'url', 'string', 'sentry URL (%)');
		Validators::assertField($config, 'enabled', 'bool');

		$builder->addDefinition($this->prefix('logger'))
			->setFactory(SentryLogger::class, [$config]);
	}

	/**
	 * Decorate services
	 */
	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults);
		if ($config['enabled'] === false) return;

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

		$builder->getDefinition($logger)
			->addSetup('addLogger', ['@' . $this->prefix('logger')]);
	}

}
