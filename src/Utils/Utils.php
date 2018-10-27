<?php declare(strict_types = 1);

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
	 * @param string|Exception $message
	 */
	public static function formatMessage($message): string
	{
		if ($message instanceof Exception) {
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
	 * @param string|Exception $message
	 * @param string $exceptionFile
	 */
	public static function formatLogLine($message, $exceptionFile = null): string
	{
		return implode(' ', [
			@date('[Y-m-d H-i-s]'), // @ timezone may not be set
			preg_replace('#\s*\r?\n\s*#', ' ', self::formatMessage($message)),
			' @  ' . Helpers::getSource(),
			$exceptionFile ? ' @@  ' . basename($exceptionFile) : null,
		]);
	}

	public static function dumpException(Throwable $exception, string $file, ?BlueScreen $blueScreen = null): string
	{
		$bs = $blueScreen ?: new BlueScreen();
		$bs->renderToFile($exception, $file);

		return $file;
	}

	public static function captureException(Throwable $exception, string $file, ?BlueScreen $blueScreen = null): string
	{
		$bs = $blueScreen ?: new BlueScreen();

		ob_start();
		$bs->renderToFile($exception, $file);

		return ob_get_contents();
	}

}
