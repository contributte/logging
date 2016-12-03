<?php

namespace Contributte\Logging\Tracy\Logger\Slack;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
final class SlackContextAttachment
{

	/** @var array */
	private $data = [];

	/** @var SlackContextField[] */
	private $fields = [];

	/**
	 * @param string $fallback
	 * @return void
	 */
	public function setFallback($fallback)
	{
		$this->data['fallback'] = $fallback;
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
	 * @param string $pretext
	 * @return void
	 */
	public function setPretext($pretext)
	{
		$this->data['pretext'] = $pretext;
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
	 * @param string $title
	 * @return void
	 */
	public function setTitle($title)
	{
		$this->data['title'] = $title;
	}

	/**
	 * @param string $link
	 * @return void
	 */
	public function setTitleLink($link)
	{
		$this->data['title_link'] = $link;
	}

	/**
	 * @param string $name
	 * @return void
	 */
	public function setAuthorName($name)
	{
		$this->data['author_name'] = $name;
	}

	/**
	 * @param string $link
	 * @return void
	 */
	public function setAuthorLink($link)
	{
		$this->data['author_link'] = $link;
	}

	/**
	 * @param string $icon
	 * @return void
	 */
	public function setAuthorIcon($icon)
	{
		$this->data['author_icon'] = $icon;
	}

	/**
	 * @param string $url
	 * @return void
	 */
	public function setImageUrl($url)
	{
		$this->data['image_url'] = $url;
	}

	/**
	 * @param string $url
	 * @return void
	 */
	public function setThumbUrl($url)
	{
		$this->data['thumb_url'] = $url;
	}

	/**
	 * @param string $footer
	 * @return void
	 */
	public function setFooter($footer)
	{
		$this->data['footer'] = $footer;
	}

	/**
	 * @param string $icon
	 * @return void
	 */
	public function setFooterIcon($icon)
	{
		$this->data['footer_icon'] = $icon;
	}

	/**
	 * @param string $timestamp
	 * @return void
	 */
	public function setTimestamp($timestamp)
	{
		$this->data['ts'] = $timestamp;
	}

	/**
	 * @param bool $markdown
	 * @return void
	 */
	public function setMarkdown($markdown = TRUE)
	{
		if ($markdown) {
			$this->data['mrkdwn_in'] = ['pretext', 'text', 'fields'];
		}
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

		return $data;
	}

}
