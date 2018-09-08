<?php

namespace Contributte\Logging\DI;

use Contributte\Logging\Sentry\SentryLogger;
use Contributte\Logging\UniversalLogger;
use Nette\DI\CompilerExtension;
use Nette\DI\ServiceCreationException;
use Nette\Utils\Validators;

final class SentryLoggingExtension extends CompilerExtension
{

	/** @var array */
	private $defaults = [
		'url' => NULL,
		'enabled' => TRUE,
	];

	/**
	 * Register services
	 *
	 * @return void
	 */
	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults);
		if ($config['enabled'] === FALSE) return;

		Validators::assertField($config, 'url', 'string', 'sentry URL (%)');
		Validators::assertField($config, 'enabled', 'bool');

		$builder->addDefinition($this->prefix('logger'))
			->setClass(SentryLogger::class, [$config]);
	}

	/**
	 * Decorate services
	 *
	 * @return void
	 */
	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults);
		if ($config['enabled'] === FALSE) return;

		$logger = $builder->getByType(UniversalLogger::class);
		if ($logger === NULL) {
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
