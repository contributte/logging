<?php declare(strict_types = 1);

namespace Contributte\Logging\Utils;

use Throwable;
use Tracy\BlueScreen;

/**
 * Based on official Tracy\Logger (@copyright David Grudl)
 *
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
final class Utils
{

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
		$contents = ob_get_contents();

		return (string) $contents;
	}

}
