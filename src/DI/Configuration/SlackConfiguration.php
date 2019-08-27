<?php declare(strict_types = 1);

namespace Contributte\Logging\DI\Configuration;

use Contributte\Logging\Slack\Formatter\ColorFormatter;
use Contributte\Logging\Slack\Formatter\ContextFormatter;
use Contributte\Logging\Slack\Formatter\ExceptionFormatter;
use Contributte\Logging\Slack\Formatter\ExceptionPreviousExceptionsFormatter;
use Contributte\Logging\Slack\Formatter\ExceptionStackTraceFormatter;

final class SlackConfiguration
{

	/** @var string */
	public $url;

	/** @var int|null */
	public $timeout = null;

	/** @var string */
	public $channel;

	/** @var string */
	public $username = 'Tracy';

	/** @var string */
	public $icon_emoji = ':rocket:';

	/** @var string|null */
	public $icon_url = null;

	/** @var string[] */
	public $formatters = [
		ContextFormatter::class,
		ColorFormatter::class,
		ExceptionFormatter::class,
		ExceptionStackTraceFormatter::class,
		ExceptionPreviousExceptionsFormatter::class,
	];

}
