# vperyod.errorlog-handler
Log exceptions to [Psr\Log]

[![Latest version][ico-version]][link-packagist]
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]

## Installation
```
composer require vperyod/errorlog-handler
```

## Usage
See [Psr\Log] documentation.
```php
// Create handler with a Logger instance
$handler = new Vperyod\ErrorlogHandler\ErrorlogHandler($logger);

// Optionally set the level to log (default:alert)
$handler->setLogLevel(LogLevel::ERROR);

// Optionally disable rethrowing if there are no other handlers for errors
$handler->setReThrow(false);

// Add to your middleware stack, radar, relay, etc.
$stack->middleware($handler);
```
[Psr\Log]: https://github.com/php-fig/log

[ico-version]: https://img.shields.io/packagist/v/vperyod/errorlog-handler.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/vperyod/vperyod.errorlog-handler/develop.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/vperyod/vperyod.errorlog-handler.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/vperyod/vperyod.errorlog-handler.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/vperyod/errorlog-handler
[link-travis]: https://travis-ci.org/vperyod/vperyod.errorlog-handler
[link-scrutinizer]: https://scrutinizer-ci.com/g/vperyod/vperyod.errorlog-handler
[link-code-quality]: https://scrutinizer-ci.com/g/vperyod/vperyod.errorlog-handler
