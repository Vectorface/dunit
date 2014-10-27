# DUnit

DUnit (dee-unit) makes your life easier by allowing you to test your PHP code
across multiple versions of PHP. [Docker](https://www.docker.com/whatisdocker/)
is used to provide an isolated test environment for each version of PHP. Out of
the box DUnit can perform a syntax check against your whole repository and run a
[PHPUnit](https://phpunit.de/) test suite.

## Supported versions of PHP

DUnit currently supports:
* PHP 5.2 (Must be specified in .dunitconfig)
* PHP 5.3
* PHP 5.4
* PHP 5.5
* PHP 5.6

and has the following extensions installed:

* apc (apcu on PHP 5.5 and newer)
* curl
* gd
* intl (PHP 5.3 and newer)
* json
* mcrypt

## Installation

Simply add the following to your composer.json `require-dev` field:

    "require-dev": {
        "vectorface/dunit": "~1.0.0"
    }

and run `composer update`.

## Usage

```shell
# run PHP syntax checks and your test suite against all supported version of PHP
$ ./vendor/bin/dunit

# show the help documentation
$ ./vendor/bin/dunit -h

# specify a custom configuration file
$ ./vendor/bin/dunit -c "path/.dunitconf"

# explictly specify which versions of PHP to use
$ ./vendor/bin/dunit -p "5.3 5.4"
```

## Configuration

Everything is customizable through the file `.dunitconfig` file. During
`composer install` the `.dunitconfig` file will be added to your root directory.
