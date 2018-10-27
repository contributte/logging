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
			->setClass(SlackLogger::class, [$config]);

		foreach ($config['formatters'] as $n => $formatter) {
			$builder->addDefinition($this->prefix('formatter.' . ($n + 1)))
				->setClass($formatter);
		}
	}

	/**
	 * Decorate services
	 */
	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

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

		$builder->getDefinition($logger)
			->addSetup('addLogger', ['@' . $this->prefix('logger')]);

		foreach ($builder->findByType(IFormatter::class) as $def) {
			$builder->getDefinition($this->prefix('logger'))
				->addSetup('addFormatter', [$def]);
		}
	}

}
