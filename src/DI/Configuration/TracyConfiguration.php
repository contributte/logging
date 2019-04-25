<?php declare(strict_types = 1);

namespace Contributte\Logging\DI\Configuration;

use Nette\DI\Definitions\Statement;

final class TracyConfiguration
{

	/** @var string */
	public $logDir;

	/** @var array[][]|string[]|Statement[]|null */
	public $loggers = null;

}
