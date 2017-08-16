# Logging

## Content

- [Universal - how to use](#tracyloggingextension)
- [Slack - how to use](#slackloggingextension)
- [Sentry - how to use](#sentryloggingextension)

## TracyLoggingExtension

First of all, we need to register our universal tuned logger for future purpose.

```yaml
extensions:
    logging: Contributte\Logging\DI\TracyLoggingExtension
```

A few configuration options:

```yaml
logging:
    logDir: %appDir%/../log
    mailer: 
        from: my@app.com
        to: [my@email.com]
```

Basically, it overrides tracy default logger & mailer by universal, pluggable instance of logger.

## SlackLoggingExtension

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

## SentryLoggingExtension

You have to require Sentry library

```
composer require sentry/sentry
```

Register extensions


```yaml
extensions:
    logging: Contributte\Logging\DI\TracyLoggingExtension
    sentry: Contributte\Logging\DI\SentryLoggingExtension
```

Fill sentry url

```yaml
sentry:
    url: https://<key>:<secret>@sentry.io/<project>
```