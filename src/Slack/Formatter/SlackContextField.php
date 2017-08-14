<?php

namespace Contributte\Logging\Slack\Formatter;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
final class SlackContextField
{

	/** @var array */
	private $data = [];

	/**
	 * @param string $title
	 * @return void
	 */
	public function setTitle($title)
	{
		$this->data['title'] = $title;
	}

	/**
	 * @param string $value
	 * @return void
	 */
	public function setValue($value)
	{
		$this->data['value'] = $value;
	}

	/**
	 * @param bool $short
	 * @return void
	 */
	public function setShort($short = TRUE)
	{
		$this->data['short'] = $short;
	}

	/**
	 * @return array
	 */
	public function toArray()
	{
		return $this->data;
	}

}
