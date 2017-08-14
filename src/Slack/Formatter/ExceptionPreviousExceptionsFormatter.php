<?php

namespace Contributte\Logging\Slack\Formatter;

use Exception;
use Throwable;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
final class ExceptionPreviousExceptionsFormatter implements IFormatter
{

	/**
	 * @param SlackContext $context
	 * @param Exception|Throwable $exception
	 * @param string $priority
	 * @return SlackContext
	 */
	public function format(SlackContext $context, $exception, $priority)
	{
		$context = clone $context;

		while (($previous = $exception->getPrevious()) != NULL) {
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
			$code->setValue($previous->getCode());
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
