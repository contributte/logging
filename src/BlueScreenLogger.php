<?php

namespace Contributte\Logging;

use Contributte\Logging\Exceptions\Logical\InvalidStateException;
use Contributte\Logging\Utils\Utils;
use Exception;
use Throwable;
use Tracy\BlueScreen;

/**
 * TracyBlueScreenLogger based on official Tracy\Logger (@copyright David Grudl)
 *
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
class BlueScreenLogger extends AbstractLogger implements ILogger
{

	/** @var BlueScreen */
	private $blueScreen;

	/**
	 * @param string $directory
	 * @param BlueScreen $blueScreen
	 */
	public function __construct($directory, BlueScreen $blueScreen = NULL)
	{
		parent::__construct($directory);
		$this->blueScreen = $blueScreen;
	}

	/**
	 * @param string|Exception|Throwable $message
	 * @param string $priority
	 * @return void
	 */
	public function log($message, $priority)
	{
		if (!is_dir($this->directory)) {
			throw new InvalidStateException('Directory ' . $this->directory . ' is not found or is not directory.');
		}

		$exceptionFile = ($message instanceof Exception || $message instanceof Throwable) ? $this->getExceptionFile($message) : NULL;

		if ($exceptionFile) {
			Utils::dumpException($message, $exceptionFile, $this->blueScreen);
		}
	}

}
