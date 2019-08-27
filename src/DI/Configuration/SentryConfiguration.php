<?php declare(strict_types = 1);

namespace Contributte\Logging\DI\Configuration;

/**
 * @see \Contributte\Logging\Sentry\SentryLogger is not sealed, so this configuration object should not also
 */
class SentryConfiguration
{

	/** @var string */
	public $url;

	/** @var bool */
	public $enabled = true;

	/** @var mixed[] */
	public $options = [];

}
