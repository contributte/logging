<?php declare(strict_types = 1);

namespace Contributte\Logging;

use Tracy\ILogger as TracyLogger;

class NullLogger implements TracyLogger
{

	/**
	 * @param mixed $message
	 * @param string $priority
	 */
	public function log($message, $priority = self::INFO): void
	{
	}

}
