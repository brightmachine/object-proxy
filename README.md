# Rapper

## What is Rapper?

This trait can help with writing facades where you want some methods to forward on to a target object
or to create a proxy class, for example if you don't want to reference vendor code from within your domain.

## Requirements

- PHP 5.4+

## Installation

Install composer in your project:

```
curl -s http://getcomposer.org/installer | php
```

Create a `composer.json` file in your project root:

```json
{
    "require": {
        "brightmachine/rapper": "*"
    }
}
```

Install via composer:

```
php composer.phar install
```

## License

Rapper is open-sourced software licensed under the MIT License - see the LICENSE file for details

## Documentation

Currently this trait allows you to do the following:

1. set the target object for proxying to
2. define a map of different function names to use

Example:

```php
<?php namespace Example;

use Monolog\Logger;
use BrightMachine\ObjectWrapper;

/**
 * Class Logger
 *
 * Proxy requests to Monolog/Logger
 *
 * @method mixed logMessage($msg) add a debug message
 * @package Example\Logger
 */
class Logger
{
    use ObjectWrapper;

    public function __construct ()
    {
        $target = new Logger('dev', $this->app['events']);
        $this->setTargetObject($target)
             ->setProxyFunctionMap([
                 'logMessage' => 'addDebug'
             ]);
    }
}
```

## Contributing

Checkout master source code from github:

```
hub clone brightmachine/rapper
```

Install development components via composer:

```
# If you don't have composer.phar
./scripts/bundle-devtools.sh .

# If you have composer.phar
composer.phar install --dev
```

### Coding Standard

We follows coding standard [PSR-2][].

Check if your codes follows PSR-2 by phpcs:

```
./vendor/bin/phpcs --standard=PSR2 src/
```

## Acknowledgement

Git repo skeleton by "[Goodby Setup](http://bit.ly/byesetup)".

[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md