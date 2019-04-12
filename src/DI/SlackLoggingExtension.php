<?php declare(strict_types = 1);

namespace Contributte\Logging\DI;

use Contributte\Logging\Slack\Formatter\ColorFormatter;
use Contributte\Logging\Slack\Formatter\ContextFormatter;
use Contributte\Logging\Slack\Formatter\ExceptionFormatter;
use Contributte\Logging\Slack\Formatter\ExceptionPreviousExceptionsFormatter;
use Contributte\Logging\Slack\Formatter\ExceptionStackTraceFormatter;
use Contributte\Logging\Slack\Formatter\IFormatter;
use Contributte\Logging\Slack\SlackLogger;
use Contributte\Logging\UniversalLogger;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\FactoryDefinition;
use Nette\DI\ServiceCreationException;
use Nette\Utils\Validators;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
final class SlackLoggingExtension extends CompilerExtension
{

	/** @var mixed[] */
	private $defaults = [
		'url' => null,
		'channel' => null,
		'username' => 'Tracy',
		'icon_emoji' => ':rocket:',
		'icon_url' => null,
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
	 */
	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults);

		Validators::assertField($config, 'url', 'string', 'slack URL (%)');
		Validators::assertField($config, 'channel', 'string', 'slack channel (%)');

		$builder->addDefinition($this->prefix('logger'))
			->setFactory(SlackLogger::class, [$config]);

		foreach ($config['formatters'] as $n => $formatter) {
			$builder->addDefinition($this->prefix('formatter.' . ($n + 1)))
				->setType($formatter);
		}
	}

	/**
	 * Decorate services
	 */
	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		$universalLogger = $builder->getByType(UniversalLogger::class);
		if ($universalLogger === null) {
			throw new ServiceCreationException(
				sprintf(
					'Service "%s" is required. Did you register %s extension as well?',
					UniversalLogger::class,
					TracyLoggingExtension::class
				)
			);
		}

		$universalLoggerDef = $builder->getDefinition($universalLogger);
		// nette v3 compatibility
		if ($universalLoggerDef instanceof FactoryDefinition) {
			$universalLoggerDef = $universalLoggerDef->getResultDefinition();
		}
		assert(method_exists($universalLoggerDef, 'addSetup'));

		$loggerDef = $builder->getDefinition($this->prefix('logger'));
		$universalLoggerDef->addSetup('addLogger', [$loggerDef]);

		// nette v3 compatibility
		if ($loggerDef instanceof FactoryDefinition) {
			$loggerDef = $loggerDef->getResultDefinition();
		}
		assert(method_exists($loggerDef, 'addSetup'));

		foreach ($builder->findByType(IFormatter::class) as $formatter) {
			$loggerDef->addSetup('addFormatter', [$formatter]);
		}
	}

}
