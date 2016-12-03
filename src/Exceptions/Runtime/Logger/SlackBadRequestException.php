<?php

namespace Contributte\Logging\Exceptions\Runtime\Logger;

use Contributte\Logging\Exceptions\RuntimeException;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
final class SlackBadRequestException extends RuntimeException
{

	/** @var array */
	private $request;

	/**
	 * @param array $request
	 */
	public function __construct(array $request)
	{
		parent::__construct();
		$this->request = $request;
	}

	/**
	 * @return array
	 */
	public function getRequest()
	{
		return $this->request;
	}

}
