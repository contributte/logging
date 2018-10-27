<?php declare(strict_types = 1);

namespace Contributte\Logging;

use Contributte\Logging\Exceptions\Logical\InvalidStateException;
use Contributte\Logging\Utils\Utils;
use Exception;
use Tracy\BlueScreen;

/**
 * BlueScreenFileLogger based on official Tracy\Logger (@copyright David Grudl)
 *
 * Log every exception as single html file
 *
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
class BlueScreenFileLogger extends AbstractLogger implements ILogger
{

	/** @var BlueScreen */
	private $blueScreen;

	public function __construct(string $directory, ?BlueScreen $blueScreen = null)
	{
		parent::__construct($directory);
		$this->blueScreen = $blueScreen;
	}

	/**
	 * @param string|Exception $message
	 * @param string $priority
	 */
	public function log($message, $priority): void
	{
		if (!is_dir($this->directory)) {
			throw new InvalidStateException('Directory ' . $this->directory . ' is not found or is not directory.');
		}

		$exceptionFile = ($message instanceof Exception) ? $this->getExceptionFile($message) : null;

		if ($exceptionFile) {
			Utils::dumpException($message, $exceptionFile, $this->blueScreen);
		}
	}

}
