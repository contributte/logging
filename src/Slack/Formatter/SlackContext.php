<?php

namespace Contributte\Logging\Slack\Formatter;

use Nette\Utils\Arrays;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
final class SlackContext
{

	/** @var array */
	private $config = [];

	/** @var array */
	private $data = [];

	/** @var SlackContextField[] */
	private $fields = [];

	/** @var SlackContextAttachment[] */
	private $attachments = [];

	/**
	 * @param array $config
	 */
	public function __construct(array $config)
	{
		$this->config = $config;
	}

	/**
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function getConfig($key, $default = NULL)
	{
		if (func_num_args() > 1) {
			$value = Arrays::get($this->config, explode('.', $key), $default);
		} else {
			$value = Arrays::get($this->config, explode('.', $key));
		}

		return $value;
	}

	/**
	 * FIELDS ******************************************************************
	 */

	/**
	 * @param string $channel
	 * @return void
	 */
	public function setChannel($channel)
	{
		$this->data['channel'] = $channel;
	}

	/**
	 * @param string $username
	 * @return void
	 */
	public function setUsername($username)
	{
		$this->data['username'] = $username;
	}

	/**
	 * @param string $icon
	 * @return void
	 */
	public function setIconEmoji($icon)
	{
		$this->data['icon_emoji'] = sprintf(':%s:', trim($icon, ':'));
	}

	/**
	 * @param string $icon
	 * @return void
	 */
	public function setIconUrl($icon)
	{
		$this->data['icon_url'] = $icon;
	}

	/**
	 * @param string $text
	 * @return void
	 */
	public function setText($text)
	{
		$this->data['text'] = $text;
	}

	/**
	 * @param string $color
	 * @return void
	 */
	public function setColor($color)
	{
		$this->data['color'] = $color;
	}

	/**
	 * @param bool $markdown
	 * @return void
	 */
	public function setMarkdown($markdown = TRUE)
	{
		$this->data['mrkdwn'] = $markdown;
	}

	/**
	 * @return SlackContextField
	 */
	public function createField()
	{
		$this->fields[] = $field = new SlackContextField();

		return $field;
	}

	/**
	 * @return SlackContextAttachment
	 */
	public function createAttachment()
	{
		$this->attachments[] = $attachment = new SlackContextAttachment();

		return $attachment;
	}

	/**
	 * @return array
	 */
	public function toArray()
	{
		$data = $this->data;

		if ($this->fields) {
			$data['fields'] = [];
			foreach ($this->fields as $attachment) {
				$data['fields'][] = $attachment->toArray();
			}
		}

		if ($this->attachments) {
			$data['attachments'] = [];
			foreach ($this->attachments as $attachment) {
				$data['attachments'][] = $attachment->toArray();
			}
		}

		return $data;
	}

}
