<?php

namespace Contributte\Logging\DI;

use Contributte\Logging\BlueScreenFileLogger;
use Contributte\Logging\ExceptionFileLogger;
use Contributte\Logging\UniversalLogger;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Nette\DI\Statement;
use Nette\Utils\Validators;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
final class TracyLoggingExtension extends CompilerExtension
{

	/** @var array */
	private $defaults = [
		'logDir' => NULL,
		'loggers' => NULL,
	];

	/**
	 * Register services
	 *
	 * @return void
	 */
	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults, $this->config);

		Validators::assertField($config, 'logDir', 'string', 'logging directory (%)');
		Validators::assertField($config, 'loggers', 'array|null');

		$logger = $builder->addDefinition($this->prefix('logger'))
			->setClass(UniversalLogger::class);

		if ($config['loggers'] === NULL) {
			$exceptionFileLogger = $builder->addDefinition($this->prefix('logger.exceptionfilelogger'))
				->setClass(ExceptionFileLogger::class, [$config['logDir']])
				->setAutowired('self');

			$blueScreenFileLogger = $builder->addDefinition($this->prefix('logger.bluescreenfilelogger'))
				->setClass(BlueScreenFileLogger::class, [$config['logDir']])
				->setAutowired('self');

			$logger->addSetup('addLogger', [$exceptionFileLogger]);
			$logger->addSetup('addLogger', [$blueScreenFileLogger]);
		}
	}

	/**
	 * Decorate services
	 *
	 * @return void
	 */
	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults, $this->config);

		// Remove tracy default logger
		if ($builder->hasDefinition('tracy.logger')) {
			$builder->removeDefinition('tracy.logger');
			$builder->addAlias('tracy.logger', $this->prefix('logger'));
		}

		// Obtain universal logger
		$universal = $builder->getDefinition($this->prefix('logger'));

		// Register defined loggers
		if ($config['loggers'] !== NULL) {
			$loggers = 1;
			foreach ($config['loggers'] as $service) {

				// Create logger as service
				if (
					is_array($service)
					|| $service instanceof Statement
					|| (is_string($service) && substr($service, 0, 1) === '@')
				) {
					$def = $builder->addDefinition($this->prefix('logger' . ($loggers++)));
					Compiler::loadDefinition($def, $service);
				} else {
					$def = $builder->getDefinitionByType($service);
				}

				$universal->addSetup('addLogger', [$def]);
			}
		}
	}

}
