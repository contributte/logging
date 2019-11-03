<?php declare(strict_types = 1);

namespace Contributte\Logging\Slack\Formatter;

use Throwable;

final class ContextFormatter implements IFormatter
{

	public function format(SlackContext $context, Throwable $message, string $priority): SlackContext
	{
		$context = clone $context;

		$context->setChannel($context->getConfig('channel'));
		$context->setUsername($context->getConfig('username', 'Tracy'));
		$context->setIconEmoji($context->getConfig('icon_emoji', 'rocket'));
		$context->setIconUrl($context->getConfig('icon_emoji', null));
		$context->setText(':bangbang::bangbang::bangbang: Exception occured :bangbang::bangbang::bangbang:');
		$context->setMarkdown();

		return $context;
	}

}
