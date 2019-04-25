<?php declare(strict_types = 1);

namespace Contributte\Logging\Slack\Formatter;

use Contributte\Logging\DI\Configuration\SlackConfiguration;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
final class SlackContext
{

	/** @var SlackConfiguration */
	private $config;

	/** @var mixed[] */
	private $data = [];

	/** @var SlackContextField[] */
	private $fields = [];

	/** @var SlackContextAttachment[] */
	private $attachments = [];

	public function __construct(SlackConfiguration $config)
	{
		$this->config = $config;
	}

	public function getConfig(): SlackConfiguration
	{
		return $this->config;
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

	public function setIconUrl(string $iconUrl): void
	{
		$this->data['icon_url'] = $iconUrl;
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
