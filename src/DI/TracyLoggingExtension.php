<?php declare(strict_types = 1);

namespace Contributte\Logging\DI;

use Contributte\Logging\BlueScreenFileLogger;
use Contributte\Logging\FileLogger;
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

	/** @var mixed[] */
	private $defaults = [
		'logDir' => null,
		'loggers' => null,
	];

	/**
	 * Register services
	 */
	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults, $this->config);

		Validators::assertField($config, 'logDir', 'string', 'logging directory (%)');
		Validators::assertField($config, 'loggers', 'array|null');

		$logger = $builder->addDefinition($this->prefix('logger'))
			->setType(UniversalLogger::class);

		if ($builder->hasDefinition('tracy.logger')) {
			$builder->addDefinition($this->prefix('originalLogger'), $builder->getDefinition('tracy.logger'))
				->setAutowired(false);
		}

		if ($config['loggers'] === null) {
			$fileLogger = $builder->addDefinition($this->prefix('logger.filelogger'))
				->setFactory(FileLogger::class, [$config['logDir']])
				->setAutowired('self');

			$blueScreenFileLogger = $builder->addDefinition($this->prefix('logger.bluescreenfilelogger'))
				->setFactory(BlueScreenFileLogger::class, [$config['logDir']])
				->setAutowired('self');

			$logger->addSetup('addLogger', [$fileLogger]);
			$logger->addSetup('addLogger', [$blueScreenFileLogger]);
		}
	}

	/**
	 * Decorate services
	 */
	public function beforeCompile(): void
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
		if ($config['loggers'] !== null) {
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
