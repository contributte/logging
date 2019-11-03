<?php declare(strict_types = 1);

namespace Contributte\Logging\Slack\Formatter;

use Throwable;

final class ExceptionPreviousExceptionsFormatter implements IFormatter
{

	public function format(SlackContext $context, Throwable $exception, string $priority): SlackContext
	{
		$context = clone $context;

		while (($previous = $exception->getPrevious()) !== null) {
			$attachment = $context->createAttachment();
			$attachment->setFallback('Required plain-text summary of the attachment.');
			$attachment->setText(sprintf('*Previous exception* (_%s_)', $previous->getMessage()));
			$attachment->setMarkdown();

			$message = $attachment->createField();
			$message->setTitle(':mag_right: Exception');
			$message->setValue(get_class($previous));

			$message = $attachment->createField();
			$message->setTitle(':envelope: Message');
			$message->setValue($previous->getMessage());
			$message->setShort();

			$code = $attachment->createField();
			$code->setTitle(':1234: Code');
			$code->setValue((string) $previous->getCode());
			$code->setShort();

			$file = $attachment->createField();
			$file->setTitle(':open_file_folder: File');
			$file->setValue('```' . $previous->getFile() . ':' . $previous->getLine() . '```');

			// Change pointer
			$exception = $previous;
		}

		return $context;
	}

}
