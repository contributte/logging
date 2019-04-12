<?php declare(strict_types = 1);

namespace Contributte\Logging\DI;

use Contributte\Logging\Sentry\SentryLogger;
use Contributte\Logging\UniversalLogger;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\FactoryDefinition;
use Nette\DI\ServiceCreationException;
use Nette\Utils\Validators;

final class SentryLoggingExtension extends CompilerExtension
{

	/** @var mixed[] */
	private $defaults = [
		'url' => null,
		'enabled' => true,
		'options' => [],
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
		Validators::assertField($config, 'options', 'array');

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

		$def = $builder->getDefinition($logger);

		// nette v3 compatibility
		if ($def instanceof FactoryDefinition) {
			$def = $def->getResultDefinition();
		}
		assert(method_exists($def, 'addSetup'));

		$def->addSetup('addLogger', ['@' . $this->prefix('logger')]);
	}

}
