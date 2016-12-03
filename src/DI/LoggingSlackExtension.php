<?php

namespace Contributte\Logging\DI;

use Contributte\Logging\Tracy\Logger\Slack\ColorFormatter;
use Contributte\Logging\Tracy\Logger\Slack\ContextFormatter;
use Contributte\Logging\Tracy\Logger\Slack\ExceptionFormatter;
use Contributte\Logging\Tracy\Logger\Slack\ExceptionPreviousExceptionsFormatter;
use Contributte\Logging\Tracy\Logger\Slack\ExceptionStackTraceFormatter;
use Contributte\Logging\Tracy\Logger\Slack\IFormatter;
use Contributte\Logging\Tracy\Logger\SlackLogger;
use Contributte\Logging\Tracy\UniversalLogger;
use Nette\DI\CompilerExtension;
use Nette\DI\ServiceCreationException;
use Nette\Utils\Validators;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
final class LoggingSlackExtension extends CompilerExtension
{

	/** @var array */
	private $defaults = [
		'url' => NULL,
		'channel' => NULL,
		'username' => 'Tracy',
		'icon_emoji' => ':rocket:',
		'icon_url' => NULL,
		'formatters' => [
			ContextFormatter::class,
			ColorFormatter::class,
			ExceptionFormatter::class,
			ExceptionStackTraceFormatter::class,
			ExceptionPreviousExceptionsFormatter::class,
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

		Validators::assertField($config, 'url', 'string', 'slack URL (%)');
		Validators::assertField($config, 'channel', 'string', 'slack channel (%)');

		$builder->addDefinition($this->prefix('logger'))
			->setClass(SlackLogger::class, [$this->defaults]);

		foreach ($config['formatters'] as $n => $formatter) {
			$builder->addDefinition($this->prefix('formatter.' . ($n + 1)))
				->setClass($formatter);
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

		$logger = $builder->getByType(UniversalLogger::class);
		if ($logger === NULL) {
			throw new ServiceCreationException(
				sprintf(
					'Service "%s" is required. Did you register %s extension as well?',
					UniversalLogger::class,
					LoggingTracyUniversalExtension::class
				)
			);
		}

		$builder->getDefinition($logger)
			->addSetup('addLogger', ['@' . $this->prefix('logger')]);

		foreach ($builder->getByType(IFormatter::class) as $def) {
			$builder->getDefinition($this->prefix('logger'))
				->addSetup('addFormatter', [$def]);
		}
	}

}
