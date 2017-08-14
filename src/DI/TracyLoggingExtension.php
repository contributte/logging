<?php

namespace Contributte\Logging\DI;

use Contributte\Logging\BlueScreenLogger;
use Contributte\Logging\FileLogger;
use Contributte\Logging\Mailer\TracyMailer;
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
		'mailer' => [
			'from' => NULL,
			'to' => NULL,
		],
		'loggers' => [
			FileLogger::class,
			BlueScreenLogger::class,
		],
		'listeners' => [],
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
		Validators::assertField($config['mailer'], 'from', 'string|null', 'mailer from (mailer.%)');
		Validators::assertField($config['mailer'], 'to', 'array', 'mailer to (mailer.%)');
		Validators::assertField($config, 'loggers', 'array');
		Validators::assertField($config, 'listeners', 'array');

		$builder->addDefinition($this->prefix('logger'))
			->setClass(UniversalLogger::class);

		$builder->addDefinition($this->prefix('logger.filelogger'))
			->setClass(FileLogger::class, [$config['logDir']])
			->setAutowired('self');

		$builder->addDefinition($this->prefix('logger.bluescreenlogger'))
			->setClass(BlueScreenLogger::class, [$config['logDir']])
			->setAutowired('self');

		$builder->addDefinition($this->prefix('mailer'))
			->setClass(TracyMailer::class, [$config['mailer']['from'], $config['mailer']['to']]);
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

		// Register defined listeners
		$listeners = 1;
		foreach ($config['listeners'] as $service) {

			// Create listener as service
			if (
				is_array($service)
				|| $service instanceof Statement
				|| (is_string($service) && substr($service, 0, 1) === '@')
			) {
				$def = $builder->addDefinition($this->prefix('listener' . ($listeners++)));
				Compiler::loadDefinition($def, $service);
			} else {
				$def = $builder->getDefinitionByType($service);
			}

			$universal->addSetup('addListener', [$def]);
		}
	}

}
