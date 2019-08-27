<?php declare(strict_types = 1);

namespace Contributte\Logging\DI;

use Contributte\Logging\BlueScreenFileLogger;
use Contributte\Logging\DI\Configuration\TracyConfiguration;
use Contributte\Logging\FileLogger;
use Contributte\Logging\UniversalLogger;
use Contributte\DI\Helper\ExtensionDefinitionsHelper;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\Statement;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 * @property TracyConfiguration $config
 */
final class TracyLoggingExtension extends CompilerExtension
{

	public function __construct()
	{
		$this->config = new TracyConfiguration();
	}

	public function getConfigSchema(): Schema
	{
		return Expect::from($this->config, [
			'loggers' => Expect::listOf('array|string|Nette\DI\Definitions\Statement')->nullable()->default(null),
		]);
	}

	/**
	 * Register services
	 */
	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$logger = $builder->addDefinition($this->prefix('logger'))
			->setType(UniversalLogger::class);

		if ($builder->hasDefinition('tracy.logger')) {
			$builder->getDefinition('tracy.logger')->setAutowired(false);
			$builder->addAlias($this->prefix('originalLogger'), 'tracy.logger');
		}

		if ($this->config->loggers === null) {
			$fileLogger = $builder->addDefinition($this->prefix('logger.filelogger'))
				->setFactory(FileLogger::class, [$this->config->logDir])
				->setAutowired('self');

			$blueScreenFileLogger = $builder->addDefinition($this->prefix('logger.bluescreenfilelogger'))
				->setFactory(BlueScreenFileLogger::class, [$this->config->logDir])
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

		// Remove tracy default logger
		if ($builder->hasDefinition('tracy.logger')) {
			$builder->removeDefinition('tracy.logger');
			$builder->addAlias('tracy.logger', $this->prefix('logger'));
		}

		// Obtain universal logger
		$universal = $builder->getDefinition($this->prefix('logger'));
		assert($universal instanceof ServiceDefinition);

		// Register defined loggers
		if ($this->config->loggers !== null) {
			$loggers = 1;
			$definitionsHelper = new ExtensionDefinitionsHelper($this->compiler);

			foreach ($this->config->loggers as $service) {
				$loggerDefinition = $definitionsHelper->getDefinitionFromConfig($service, 'logger' . ($loggers++));

				$universal->addSetup('addLogger', [$loggerDefinition]);
			}
		}
	}

}
