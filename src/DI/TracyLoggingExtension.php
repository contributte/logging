<?php declare(strict_types = 1);

namespace Contributte\Logging\DI;

use Contributte\Logging\BlueScreenFileLogger;
use Contributte\Logging\FileLogger;
use Contributte\Logging\UniversalLogger;
use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;

/**
 * @property-read stdClass $config
 */
final class TracyLoggingExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'logDir' => Expect::string()->required(),
			'loggers' => Expect::listOf('array|string|Nette\DI\Definitions\Statement'),
		]);
	}

	/**
	 * Register services
	 */
	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->config;

		$logger = $builder->addDefinition($this->prefix('logger'))
			->setType(UniversalLogger::class);

		// Register defined loggers
		if (count($config->loggers) !== 0) {
			$loggers = [];

			foreach ($config->loggers as $k => $v) {
				$loggers[$this->prefix('logger.' . $k)] = $v;
			}

			$this->compiler->loadDefinitionsFromConfig($loggers);

			foreach (array_keys($loggers) as $name) {
				$logger->addSetup('addLogger', [$builder->getDefinition($name)]);
			}

			return;
		}

		// Register default loggers
		$fileLogger = $builder->addDefinition($this->prefix('logger.filelogger'))
			->setFactory(FileLogger::class, [$config->logDir])
			->setAutowired('self');

		$blueScreenFileLogger = $builder->addDefinition($this->prefix('logger.bluescreenfilelogger'))
			->setFactory(BlueScreenFileLogger::class, [$config->logDir])
			->setAutowired('self');

		$logger->addSetup('addLogger', [$fileLogger]);
		$logger->addSetup('addLogger', [$blueScreenFileLogger]);
	}

	/**
	 * Decorate services
	 */
	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		// Replace tracy default logger for ours
		if ($builder->hasDefinition('tracy.logger')) {
			$builder->addDefinition($this->prefix('originalLogger'), clone $builder->getDefinition('tracy.logger'))
				->setAutowired(false);

			$builder->removeDefinition('tracy.logger');
			$builder->addAlias('tracy.logger', $this->prefix('logger'));
		}
	}

}
