<?php declare(strict_types=1);

namespace Contributte\Logging\Sentry;

use Contributte\Logging\Exceptions\Logical\InvalidStateException;
use Contributte\Logging\ILogger;
use Throwable;

class SentryLogger implements ILogger
{
    public const LEVEL_PRIORITY_MAP = [
        self::DEBUG => \Sentry\Severity::DEBUG,
        self::INFO => \Sentry\Severity::INFO,
        self::WARNING => \Sentry\Severity::WARNING,
        self::ERROR => \Sentry\Severity::ERROR,
        self::EXCEPTION => \Sentry\Severity::FATAL,
        self::CRITICAL => \Sentry\Severity::FATAL,
    ];
    public const CONFIG_URL = 'url';
    public const CONFIG_OPTIONS = 'options';

    /** @var mixed[] */
    protected $configuration;

    /** @var string[] */
    private $allowedPriority = [ILogger::ERROR, ILogger::EXCEPTION, ILogger::CRITICAL];

    /**
     * @param mixed[] $configuration
     */
    public function __construct(array $configuration)
    {
        if (!isset($configuration[self::CONFIG_URL])) {
            throw new InvalidStateException('Missing url in SentryLogger configuration');
        }

        if (!isset($configuration[self::CONFIG_OPTIONS])) {
            $configuration[self::CONFIG_OPTIONS] = [];
        }

        $this->configuration = $configuration;
    }

    /**
     * @param string[] $allowedPriority
     */
    public function setAllowedPriority(array $allowedPriority): void
    {
        $this->allowedPriority = $allowedPriority;
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
     * @param mixed $message
     */
    public function log($message, string $priority = ILogger::INFO): void
    {
        if (!in_array($priority, $this->allowedPriority, true)) {
            return;
        }

        $level = $this->getLevel($priority);

        if ($level === null) {
            return;
        }

        $scope = (new \Sentry\State\Scope())
            ->setLevel(new \Sentry\Severity($level));

        $this->makeRequest($message, $scope);
    }

    /**
     * @param mixed $message
     * @param \Sentry\State\Scope $scope
     */
    protected function makeRequest($message, \Sentry\State\Scope $scope): void
    {
        $client = \Sentry\ClientBuilder::create(
            $this->configuration[self::CONFIG_OPTIONS]
            + ['dsn' => $this->configuration[self::CONFIG_URL]]
        )->getClient();

        if ($message instanceof Throwable) {
            $client->captureException($message, $scope);
        } else {
            $client->captureMessage($message, null, $scope);
        }
    }

    protected function getLevel(string $priority): ?string
    {
        return self::LEVEL_PRIORITY_MAP[$priority] ?? null;
    }
}
