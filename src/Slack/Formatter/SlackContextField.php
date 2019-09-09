<?php declare(strict_types = 1);

namespace Contributte\Logging\Slack\Formatter;

final class SlackContextField
{

	/** @var mixed[] */
	private $data = [];

	public function setTitle(string $title): void
	{
		$this->data['title'] = $title;
	}

	public function setValue(string $value): void
	{
		$this->data['value'] = $value;
	}

	public function setShort(bool $short = true): void
	{
		$this->data['short'] = $short;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		return $this->data;
	}

}
