<?php

namespace Contributte\Logging\DI;

use Contributte\Logging\Tracy\Logger\TracyBlueScreenLogger;
use Contributte\Logging\Tracy\Logger\TracyFileLogger;
use Contributte\Logging\Tracy\Mailer\TracyMailer;
use Contributte\Logging\Tracy\UniversalLogger;
use Nette\DI\CompilerExtension;
use Nette\Utils\Validators;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
final class LoggingTracyUniversalExtension extends CompilerExtension
{

	/** @var array */
	private $defaults = [
		'logDir' => NULL,
		'mailer' => [
			'from' => NULL,
			'to' => NULL,
		],
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

		Validators::assertField($config, 'logDir', 'string', 'logging directory (%)');
		Validators::assertField($config['mailer'], 'from', 'string', 'mailer from (mailer.%)');
		Validators::assertField($config['mailer'], 'to', 'array', 'mailer to (mailer.%)');

		$builder->addDefinition($this->prefix('logger'))
			->setClass(UniversalLogger::class)
			->addSetup('addLogger', ['@' . $this->prefix('tracy.file.logger')])
			->addSetup('addLogger', ['@' . $this->prefix('tracy.bluescreen.logger')]);

		$builder->addDefinition($this->prefix('tracy.file.logger'))
			->setClass(TracyFileLogger::class, [$config['logDir']]);

		$builder->addDefinition($this->prefix('tracy.bluescreen.logger'))
			->setClass(TracyBlueScreenLogger::class, [$config['logDir']]);

		$builder->addDefinition($this->prefix('tracyMailer'))
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

		if ($builder->hasDefinition('tracy.logger')) {
			$builder->removeDefinition('tracy.logger');
			$builder->addAlias('tracy.logger', $this->prefix('logger'));
		}
	}

}
