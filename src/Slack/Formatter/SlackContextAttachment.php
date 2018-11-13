<?php declare(strict_types = 1);

namespace Contributte\Logging\Slack\Formatter;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
final class SlackContextAttachment
{

	/** @var mixed[] */
	private $data = [];

	/** @var SlackContextField[] */
	private $fields = [];

	public function setFallback(string $fallback): void
	{
		$this->data['fallback'] = $fallback;
	}

	public function setColor(string $color): void
	{
		$this->data['color'] = $color;
	}

	public function setPretext(string $pretext): void
	{
		$this->data['pretext'] = $pretext;
	}

	public function setText(string $text): void
	{
		$this->data['text'] = $text;
	}

	public function setTitle(string $title): void
	{
		$this->data['title'] = $title;
	}

	public function setTitleLink(string $link): void
	{
		$this->data['title_link'] = $link;
	}

	public function setAuthorName(string $name): void
	{
		$this->data['author_name'] = $name;
	}

	public function setAuthorLink(string $link): void
	{
		$this->data['author_link'] = $link;
	}

	public function setAuthorIcon(string $icon): void
	{
		$this->data['author_icon'] = $icon;
	}

	public function setImageUrl(string $url): void
	{
		$this->data['image_url'] = $url;
	}

	public function setThumbUrl(string $url): void
	{
		$this->data['thumb_url'] = $url;
	}

	public function setFooter(string $footer): void
	{
		$this->data['footer'] = $footer;
	}

	public function setFooterIcon(string $icon): void
	{
		$this->data['footer_icon'] = $icon;
	}

	public function setTimestamp(string $timestamp): void
	{
		$this->data['ts'] = $timestamp;
	}

	public function setMarkdown(bool $markdown = true): void
	{
		if ($markdown) {
			$this->data['mrkdwn_in'] = ['pretext', 'text', 'fields'];
		}
	}

	public function createField(): SlackContextField
	{
		$this->fields[] = $field = new SlackContextField();

		return $field;
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

		return $data;
	}

}
