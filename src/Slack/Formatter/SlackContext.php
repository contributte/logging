<?php declare(strict_types = 1);

namespace Contributte\Logging\Slack\Formatter;

use Nette\Utils\Arrays;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
final class SlackContext
{

	/** @var mixed[] */
	private $config = [];

	/** @var mixed[] */
	private $data = [];

	/** @var SlackContextField[] */
	private $fields = [];

	/** @var SlackContextAttachment[] */
	private $attachments = [];

	/**
	 * @param mixed[] $config
	 */
	public function __construct(array $config)
	{
		$this->config = $config;
	}

	/**
	 * @param mixed $default
	 * @return mixed
	 */
	public function getConfig(string $key, $default = null)
	{
		if (func_num_args() > 1) {
			$value = Arrays::get($this->config, explode('.', $key), $default);
		} else {
			$value = Arrays::get($this->config, explode('.', $key));
		}

		return $value;
	}

	public function setChannel(string $channel): void
	{
		$this->data['channel'] = $channel;
	}

	public function setUsername(string $username): void
	{
		$this->data['username'] = $username;
	}

	public function setIconEmoji(string $icon): void
	{
		$this->data['icon_emoji'] = sprintf(':%s:', trim($icon, ':'));
	}

	public function setIconUrl(string $icon): void
	{
		$this->data['icon_url'] = $icon;
	}

	public function setText(string $text): void
	{
		$this->data['text'] = $text;
	}

	public function setColor(string $color): void
	{
		$this->data['color'] = $color;
	}

	public function setMarkdown(bool $markdown = true): void
	{
		$this->data['mrkdwn'] = $markdown;
	}

	public function createField(): SlackContextField
	{
		$this->fields[] = $field = new SlackContextField();

		return $field;
	}

	public function createAttachment(): SlackContextAttachment
	{
		$this->attachments[] = $attachment = new SlackContextAttachment();

		return $attachment;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = $this->data;

		if (count($this->fields) > 0) {
			$data['fields'] = [];
			foreach ($this->fields as $attachment) {
				$data['fields'][] = $attachment->toArray();
			}
		}

		if (count($this->fields) > 0) {
			$data['attachments'] = [];
			foreach ($this->attachments as $attachment) {
				$data['attachments'][] = $attachment->toArray();
			}
		}

		return $data;
	}

}
