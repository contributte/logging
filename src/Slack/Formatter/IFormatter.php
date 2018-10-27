<?php declare(strict_types = 1);

namespace Contributte\Logging\Slack\Formatter;

use Exception;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
interface IFormatter
{

	/**
	 * @param string|Exception $message
	 * @param string $priority
	 */
	public function format(SlackContext $context, $message, $priority): SlackContext;

}
