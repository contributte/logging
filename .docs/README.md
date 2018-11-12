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

### Default loggers

There are 2 types of loggers defined by default.

- **FileLogger** - creates <priority>.log file
- **BlueScreenFileLogger** - creates exception-*.html
- **SendMailLogger** - sends exception to email

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
    public function log($message, string $priority = self::INFO)
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
composer require sentry/sentry
```

Now you should register new company/profile at Sentry's page (https://sentry.io/organizations/new/). There you 
obtained key, secret and project ID. Put these variables into neon file.

```yaml
sentry:
    url: https://<key>:<secret>@sentry.io/<project>
```
