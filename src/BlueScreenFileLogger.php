<?php declare(strict_types = 1);

namespace Contributte\Logging;

use Contributte\Logging\Exceptions\Logical\InvalidStateException;
use Contributte\Logging\Utils\Utils;
use Throwable;
use Tracy\BlueScreen;

/**
 * BlueScreenFileLogger based on official Tracy\Logger (@copyright David Grudl)
 *
 * Log every exception as single html file
 */
class BlueScreenFileLogger extends AbstractLogger implements ILogger
{

	/** @var BlueScreen|null */
	private $blueScreen;

	public function __construct(string $directory, ?BlueScreen $blueScreen = null)
	{
		parent::__construct($directory);
		$this->blueScreen = $blueScreen;
	}

	/**
	 * @param mixed $message
	 */
	public function log($message, string $priority = ILogger::INFO): void
	{
		if (!is_dir($this->directory)) {
			throw new InvalidStateException('Directory ' . $this->directory . ' is not found or is not directory.');
		}

		if ($message instanceof Throwable) {
			Utils::dumpException($message, $this->getExceptionFile($message), $this->blueScreen);
		}
	}

}
