# Logging

## Content

- [Tracy - universal logging](#tracy)
- [Slack - send exeptions to channel](#slack)
- [Sentry - send exceptions to Sentry](#sentry)

## Tracy

First of all, we need to register our universal tuned logger for the future purpose.

```yaml
extensions:
    logging: Contributte\Logging\DI\TracyLoggingExtension
```

After that, we need to setup `logDir`.

```yaml
logging:
    logDir: %appDir%/../log
```

Basically, it overrides Tracy's default logger by our universal, pluggable logger.

Original logger is still in DIC with `logging.originalLogger` key.

### Default loggers

There are 3 types of loggers defined by default.

- **FileLogger** - creates `<priority>.log` file
- **BlueScreenFileLogger** - creates exception-*.html from all throwable
- **SendMailLogger** - sends throwable/message to email

You can redefine these loggers in `logging.loggers`.

```yaml
logging:
    loggers:
        - Contributte\Logging\FileLogger(%logDir%)
        - Contributte\Logging\BlueScreenFileLogger(%logDir%)
        - Contributte\Logging\SendMailLogger(
            Contributte\Logging\Mailer\TracyMailer(
                from@email,
                [to@email, to2@email]
            ),
            %logDir%
        )
        - App\Model\MyCustomerLogger
```

This configuration is functionally equal to original Tracy's logger, only separated to multiple classes.

#### SendMailLogger

Our SendMailLogger also allows configure priority levels.

```yaml
services:
    sendMaillogger:
        setup:
            - setAllowedPriority([
                Contributte\Logging\ILogger::WARNING,
                Contributte\Logging\ILogger::ERROR
            ])
```

### Custom logger

To create your custom logger you have to implement `Contributte\Logging\ILogger`.

```php
<?php

namespace App\Model;

use Contributte\Logging\ILogger;

class MyDatabaseLogger implements ILogger
{

    /**
     * @param mixed $message
     * @return void
     */
    public function log($message, string $priority = self::INFO): void
    {
        // store exception to database...
    }

}

```

And register it in neon.

```yaml
logging:
    loggers:
        - App\Model\MyDatabaseLogger(@connection)
```

## Slack

```yaml
extensions:
    logging: Contributte\Logging\DI\TracyLoggingExtension
    slack: Contributte\Logging\DI\SlackLoggingExtension
```

There is a configuration you have to fill in.

| Key        | Requirements | Default  |
|------------|--------------|----------|
| url        | required     | -        |
| channel    | required     | -        |
| username   | optional     | Tracy    |
| icon_emoji | optional     | :rocket: |
| icon_url   | optional     | -        |

```yaml
slack:
    url: https://hooks.slack.com/services/<code1>/<code2>/<code3>
    channel: tracy
```

### Formatters

By default, there are 5 formatters for your slack-channel-pleasure.

You can disable it like this:

```yaml
slack:
    formatters: []
```

And configure your own formatters. They will be loaded automatically, if
you implement needed interface (`Contributte\Logging\Slack\Formatter\IFormatter`).

```yaml
services:
    - App\Slack\MySuperTrouperFormatter
```

#### `Contributte\Logging\Slack\Formatter\ContextFormatter`

- Setup `context` with all configured data (channel, icon, etc).

#### `Contributte\Logging\Slack\Formatter\ColorFormatter`

- `danger` -> `ILogger::CRITICAL`
- `#ff0000` -> `ILogger::EXCEPTION`
- `warning` -> `ILogger::ERROR`

#### `Contributte\Logging\Slack\Formatter\ExceptionFormatter`

![ContextFormatter](https://raw.githubusercontent.com/contributte/logging/master/.docs/assets/formatter-exception.png)

#### `Contributte\Logging\Slack\Formatter\ExceptionPreviousExceptionsFormatter`

![ContextFormatter](https://raw.githubusercontent.com/contributte/logging/master/.docs/assets/formatter-previous-exceptions.png)

#### `Contributte\Logging\Slack\Formatter\ExceptionStackTraceFormatter`

![ContextFormatter](https://raw.githubusercontent.com/contributte/logging/master/.docs/assets/formatter-stack-trace.png)

## Sentry

```yaml
extensions:
    logging: Contributte\Logging\DI\TracyLoggingExtension
    sentry: Contributte\Logging\DI\SentryLoggingExtension
```

This extension requires to have sentry installed.

```
$ composer require sentry/sdk:"^2.0"
```

Now you should go to project Settings page -> Client Keys (DSN) section.
There you obtained DNS url. Put the url into neon file.

```yaml
sentry:
    url: https://<key>@sentry.io/<project>
```

`SentryLoggingExtension` adds `SentryLogger` with url configuration. It works as [SendMailLogger](#sendmaillogger).

It means that it sends messages/throwable with `ILogger::ERROR`, `ILogger::EXCEPTION`, `ILogger::CRITICAL` priorities.

But if you need other priorities, you can change configuration.

```yaml
services:
    sentry.logger:
        setup:
            - setAllowedPriority(
                Contributte\Logging\ILogger::WARNING,
                Contributte\Logging\ILogger::ERROR
            )
```
