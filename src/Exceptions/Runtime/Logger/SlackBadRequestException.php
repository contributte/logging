<?php declare(strict_types = 1);

namespace Contributte\Logging\Exceptions\Runtime\Logger;

use Contributte\Logging\Exceptions\RuntimeException;

final class SlackBadRequestException extends RuntimeException
{

	/** @var mixed[] */
	private $request;

	/**
	 * @param mixed[] $request
	 */
	public function __construct(array $request)
	{
		parent::__construct();
		$this->request = $request;
	}

	/**
	 * @return mixed[]
	 */
	public function getRequest(): array
	{
		return $this->request;
	}

}
