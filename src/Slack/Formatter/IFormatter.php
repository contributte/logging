<?php declare(strict_types = 1);

namespace Contributte\Logging\Slack\Formatter;

interface IFormatter
{

	/**
	 * @param mixed $message
	 */
	public function format(SlackContext $context, $message, string $priority): SlackContext;

}
