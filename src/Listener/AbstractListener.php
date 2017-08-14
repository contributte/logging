<?php

namespace Contributte\Logging\Listener;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
abstract class AbstractListener implements IListener
{

	/**
	 * @param mixed $message
	 * @param string $priority
	 * @return void
	 */
	public function beforeLog($message, $priority)
	{
		// Do nothing..
	}

	/**
	 * @param mixed $message
	 * @param string $priority
	 * @return void
	 */
	public function afterLog($message, $priority)
	{
		// Do nothing..
	}

}
