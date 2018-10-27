<?php declare(strict_types=1);

namespace Contributte\Logging\Slack\Formatter;

use Tracy\Helpers;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
final class ExceptionFormatter implements IFormatter
{

	/**
	 * {@inheritdoc}
	 */
	public function format(SlackContext $context, $exception, string $priority): SlackContext
	{
		$context = clone $context;

		$attachment = $context->createAttachment();
		$attachment->setColor('danger');
		$attachment->setMarkdown();

		$message = $attachment->createField();
		$message->setTitle(':date: Date');
		$message->setValue(@date('[d.m.Y]'));
		$message->setShort();

		$message = $attachment->createField();
		$message->setTitle(':timer_clock: Time');
		$message->setValue(@date('[H:i:s]'));
		$message->setShort();

		$message = $attachment->createField();
		$message->setTitle(':computer: Source');
		$message->setValue(Helpers::getSource());

		$message = $attachment->createField();
		$message->setTitle(':mag_right: Exception');
		$message->setValue(get_class($exception));

		$message = $attachment->createField();
		$message->setTitle(':envelope: Message');
		$message->setValue($exception->getMessage());
		$message->setShort();

		$code = $attachment->createField();
		$code->setTitle(':1234: Code');
		$code->setValue($exception->getCode());
		$code->setShort();

		$file = $attachment->createField();
		$file->setTitle(':open_file_folder: File');
		$file->setValue('```' . $exception->getFile() . ':' . $exception->getLine() . '```');

		return $context;
	}

}
