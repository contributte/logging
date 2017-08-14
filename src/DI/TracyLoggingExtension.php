<?php

namespace Contributte\Logging\DI;

use Contributte\Logging\BlueScreenLogger;
use Contributte\Logging\FileLogger;
use Contributte\Logging\Mailer\TracyMailer;
use Contributte\Logging\UniversalLogger;
use Nette\DI\CompilerExtension;
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
			->setClass(FileLogger::class, [$config['logDir']]);

		$builder->addDefinition($this->prefix('tracy.bluescreen.logger'))
			->setClass(BlueScreenLogger::class, [$config['logDir']]);

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
