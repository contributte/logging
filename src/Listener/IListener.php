<?php

namespace Contributte\Logging\Listener;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
interface IListener
{

	/**
	 * @param string $message
	 * @param string $priority
	 * @return void
	 */
	public function beforeLog($message, $priority);

	/**
	 * @param string $message
	 * @param string $priority
	 * @return void
	 */
	public function afterLog($message, $priority);

}
