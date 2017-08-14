<?php

namespace Contributte\Logging\Utils;

use ErrorException;
use Exception;
use Throwable;
use Tracy\BlueScreen;
use Tracy\Dumper;
use Tracy\Helpers;

/**
 * Based on official Tracy\Logger (@copyright David Grudl)
 *
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
final class Utils
{

	/**
	 * @param string|Exception|Throwable $message
	 * @return string
	 */
	public static function formatMessage($message)
	{
		if ($message instanceof Exception || $message instanceof Throwable) {
			while ($message) {
				$tmp[] = ($message instanceof ErrorException
						? Helpers::errorTypeToString($message->getSeverity()) . ': ' . $message->getMessage()
						: Helpers::getClass($message) . ': ' . $message->getMessage() . ($message->getCode() ? ' #' . $message->getCode() : '')
					) . ' in ' . $message->getFile() . ':' . $message->getLine();
				$message = $message->getPrevious();
			}
			$message = implode("\ncaused by ", $tmp);

		} elseif (!is_string($message)) {
			$message = Dumper::toText($message);
		}

		return trim($message);
	}

	/**
	 * @param string|Exception|Throwable $message
	 * @param string $exceptionFile
	 * @return string
	 */
	public static function formatLogLine($message, $exceptionFile = NULL)
	{
		return implode(' ', [
			@date('[Y-m-d H-i-s]'), // @ timezone may not be set
			preg_replace('#\s*\r?\n\s*#', ' ', self::formatMessage($message)),
			' @  ' . Helpers::getSource(),
			$exceptionFile ? ' @@  ' . basename($exceptionFile) : NULL,
		]);
	}

	/**
	 * @param Exception|Throwable $exception
	 * @param string $file
	 * @param BlueScreen $blueScreen
	 * @return string
	 */
	public static function dumpException($exception, $file, $blueScreen = NULL)
	{
		$bs = $blueScreen ?: new BlueScreen;
		$bs->renderToFile($exception, $file);

		return $file;
	}

	/**
	 * @param Exception|Throwable $exception
	 * @param string $file
	 * @param BlueScreen $blueScreen
	 * @return string
	 */
	public static function captureException($exception, $file, $blueScreen = NULL)
	{
		$bs = $blueScreen ?: new BlueScreen;

		ob_start();
		$bs->renderToFile($exception, $file);

		return ob_get_contents();
	}

}
