<?php declare(strict_types = 1);

namespace Contributte\Logging\Slack\Formatter;

use Throwable;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
interface IFormatter
{

	/**
	 * @param string|Throwable $message
	 */
	public function format(SlackContext $context, $message, string $priority): SlackContext;

}
