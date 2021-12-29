<?php declare(strict_types = 1);

namespace Contributte\Logging;

use DirectoryIterator;
use Throwable;

/**
 * AbstractTracyLogger based on official Tracy\Logger (@copyright David Grudl)
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
		$data = [];

		while ($exception) {
			$data[] = [
				$exception->getMessage(),
				$exception->getCode(),
				$exception->getFile(),
				$exception->getLine(),
				array_map(function ($item): array {
					unset($item['args']);

					return $item;
				}, $exception->getTrace()),
			];
			$exception = $exception->getPrevious();
		}

		$hash = substr(md5(serialize($data)), 0, 10);

		foreach (new DirectoryIterator($this->directory) as $file) {
			if ($file->isDot()) {
				continue;
			}

			if ((bool) strpos($file->getBasename(), $hash)) {
				return $file->getPathname();
			}
		}

		return $this->directory . '/exception--' . @date('Y-m-d--H-i') . '--' . $hash . '.html'; // @ timezone may not be set
	}

}
