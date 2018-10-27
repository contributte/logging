<?php declare(strict_types = 1);

namespace Contributte\Logging\Slack\Formatter;

use Exception;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
final class ContextFormatter implements IFormatter
{

	/**
	 * @param string|Exception $message
	 * @param string $priority
	 */
	public function format(SlackContext $context, $message, $priority): SlackContext
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
