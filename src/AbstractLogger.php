<?php declare(strict_types = 1);

namespace Contributte\Logging;

use DirectoryIterator;
use SplFileInfo;
use Throwable;

/**
 * AbstractTracyLogger based on official Tracy\Logger (@copyright David Grudl)
 *
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
abstract class AbstractLogger implements ILogger
{

	/** @var string */
	protected $directory;

	public function __construct(string $directory)
	{
		$this->directory = $directory;
	}

	public function setDirectory(string $directory): void
	{
		$this->directory = $directory;
	}

	protected function getExceptionFile(Throwable $exception): string
	{
		while ($exception) {
			$data[] = [
				$exception->getMessage(),
				$exception->getCode(),
				$exception->getFile(),
				$exception->getLine(),
				array_map(function ($item) {
					unset($item['args']);

					return $item;
				}, $exception->getTrace()),
			];
			$exception = $exception->getPrevious();
		}
		$hash = substr(md5(serialize($data)), 0, 10);

		/** @var SplFileInfo $file */
		foreach (new DirectoryIterator($this->directory) as $file) {
			if ($file->isDot()) {
				continue;
			}
			if (strpos($file, $hash)) {
				return $file->getPathname();
			}
		}

		return $this->directory . '/exception--' . @date('Y-m-d--H-i') . '--' . $hash . '.html'; // @ timezone may not be set
	}

}
