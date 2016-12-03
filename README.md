# Logging

:boom: Universal logging support to Tracy / Nette Framework (@nette)

- Slack
- Monolog
    - Handlers
    - Formatters

-----

[![Build Status](https://img.shields.io/travis/contributte/logging.svg?style=flat-square)](https://travis-ci.org/contributte/logging)
[![Code coverage](https://img.shields.io/coveralls/contributte/logging.svg?style=flat-square)](https://coveralls.io/r/contributte/logging)
[![Downloads this Month](https://img.shields.io/packagist/dm/contributte/logging.svg?style=flat-square)](https://packagist.org/packages/contributte/logging)
[![Downloads total](https://img.shields.io/packagist/dt/contributte/logging.svg?style=flat-square)](https://packagist.org/packages/contributte/logging)
[![Latest stable](https://img.shields.io/packagist/v/contributte/logging.svg?style=flat-square)](https://packagist.org/packages/contributte/logging)
[![HHVM Status](https://img.shields.io/hhvm/contributte/logging.svg?style=flat-square)](http://hhvm.h4cc.de/package/contributte/logging)

## Discussion / Help

[![Join the chat](https://img.shields.io/gitter/room/contributte/contributte.svg?style=flat-square)](https://gitter.im/contributte/contributte?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

## Install

```
composer require contributte/logging
```

## Usage

### UniversalExtension

First of all, we need to register our universal logger for future purpose.

```yaml
extensions:
    logging: Contributte\Logging\DI\LoggingTracyUniversalExtension
```

A few settings to our extension.

```yaml
logging:
    logDir: %appDir%/../log
    mailer: 
        from: my@app.com
        to: [my@email.com]
```

Basically, it override tracy default logger & mailer by universal, pluggable instance of logger.

### SlackExtension

```yaml
extensions:
    slack: Contributte\Logging\DI\LoggingSlackExtension
```

There is a configuration you have to fill.

| Key        | Requirements | Default  |
|------------|--------------|----------|
| url        | required     | -        |
| channel    | required     | -        |
| username   | optional     | Tracy    |
| icon_emoji | optional     | :rocket: |
| icon_url   | optional     | -        |

```yaml
slack:
    url:
    channel:
```

#### Formatters

By default, there are 5 formatters for your slack-channel-pleasure.

You can disable it like this:

```yaml
slack:
    formatters: []
```

And configure your own formatters. They will be loaded automatically, if
you implement needed interface (`Contributte\Logging\Tracy\Logger\Slack\IFormatter`).

```yaml
services:
    - App\Slack\MySuperTrouperFormatter
```

##### `Contributte\Logging\Tracy\Logger\Slack\ContextFormatter`

![ContextFormatter](https://raw.githubusercontent.com/contributte/logging/docs/formatter-exception.png)

##### `Contributte\Logging\Tracy\Logger\Slack\ColorFormatter`

- `danger` -> `ILogger::CRITICAL`
- `#ff0000` -> `ILogger::EXCEPTION`
- `warning` -> `ILogger::ERROR`

##### `Contributte\Logging\Tracy\Logger\Slack\ExceptionFormatter`

![ContextFormatter](https://raw.githubusercontent.com/contributte/logging/docs/formatter-exception.png)

##### `Contributte\Logging\Tracy\Logger\Slack\ExceptionPreviousExceptionsFormatter`

![ContextFormatter](https://raw.githubusercontent.com/contributte/logging/docs/formatter-previous-exceptions.png)

##### `Contributte\Logging\Tracy\Logger\Slack\ExceptionStackTraceFormatter`

![ContextFormatter](https://raw.githubusercontent.com/contributte/logging/docs/formatter-stack-trace.png)

-----

Thank you for testing, reporting and contributing.
