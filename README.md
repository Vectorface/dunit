# DUnit

DUnit (dee-unit) makes your life easier by allowing you to test your PHP code
against multiple [Docker](https://www.docker.com/whatisdocker/) containers.
This allows for testing your code against different versions of PHP or against
different PHP configurations.
By default, DUnit can perform a syntax check against your whole repository and
run a [PHPUnit](https://phpunit.de/) test suite.

## Default Containers

DUnit includes preconfigured containers for the following PHP versions:

* PHP 5.2 (add `vectorface/php5.2` to your `.dunitconfig`)
* PHP 5.3
* PHP 5.4
* PHP 5.5
* PHP 5.6
* HHVM stable
* HHVM nightly (add `vectorface/hhvm-nightly` to your `.dunitconfig`)

and has the following native extensions installed:

* apc (apcu on PHP 5.5 and newer)
* curl
* gd
* intl (PHP 5.3 and newer)
* json
* mcrypt

## Installation

Simply run the following [composer](https://getcomposer.org/) command:

```shell
$ composer require vectorface/dunit --dev
```

It is highly recommended to copy the example config to your project root to
control the default behaviour of the `dunit` command.

```shell
$ cp ./vendor/vectorface/dunit/dunitconfig.example ./.dunitconfig
```

And edit the file `.dunitconfig` to suit your tastes.

## Usage

```shell
# run PHP syntax checks and your test suite against all supported version of PHP
$ ./vendor/bin/dunit

# show the help documentation
$ ./vendor/bin/dunit -h

# specify a custom configuration file
$ ./vendor/bin/dunit -c "path/.dunitconf"

# explictly specify which containers to use
$ ./vendor/bin/dunit -i "vectorface/php5.3 vectorface/php5.4"
```

## Configuration

There are three ways to customize the `dunit` command:
* Environment variables
* Local config file
* Script flags.

### Environment variables

The following environment variables can be set to configure dunit.

* `DUNIT_PHPVERSION` - a string like "5.3 5.4" indicates the version of PHP to
    run against.
* `DUNIT_DOCKERIMAGE` - a string like "vectorface/php" to indicate which Docker
    image to use. Note that the PHP version will be appended to the end of the
    Docker image so the actual image would be "vectorface/php5.4".
* `DUNIT_PHPSYNTAX` - a `true`/`false` flag indicating whether dunit should run
    the syntax checks.
* `DUNIT_PHPSYNTAXCOMMAND` - a string indicating the exact command dunit should
    run to perform the syntax checks. This variable is ignored if
    `DUNIT_PHPSYNTAX` is set to `false`. Note that the command runs *inside* the
    Docker container and not on your host machine.
* `DUNIT_PHPUNIT` - a `true`/`false` flag indicating whether dunit should run
    PHPUnit.
* `DUNIT_PHPUNITCOMMAND` - a string indicating the exact command `dunit` should
    execute for the unit test suite. This variable is ignored if `DUNIT_PHPUNIT`
    is set to `false`. Note that the command runs *inside* the Docker container
    and not on your host machine.

#### Examples:

Run `dunit` for versions 5.3 and 5.4 of PHP and skip the syntax check:

```shell
$ DUNIT_PHPVERSION="5.3 5.4" DUNIT_PHPSYNTAX=false ./vendor/bin/dunit
```

Run `dunit` with a custom docker image named "myDocker/customImage5.3".

```shell
$ DUNIT_PHPVERSION="5.3" DUNIT_DOCKERIMAGE="myDocker/customImage" ./vendor/bin/dunit
```

### Local config file (.dunitconfig)

The `dunit` script will check for the presence of a local file named `.dunitconfig`.
An example config file can be copied from the composer package:

```shell
$ cp ./vendor/vectorface/dunit/dunitconfig.example ./.dunitconfig
```

*The environment variables specified by the config file will override any
variables passed directly to the script.*

### Script flags

The `dunit` script can also take a number of flags. These flags will always
override conflicting environment variable settings.

* `-h` - displays help information.
* `-v` - displays the current version.
* `-c ./path/to/config` - `dunit` will use the config located at the provided
    path instead of looking in your local folder for `.dunitconfig`.
* `-i "image1 image2"` - `dunit` will only run against the specified images.

#### Examples:

Run `dunit` for versions 5.3 and 5.4 of PHP.

```shell
$ ./vendor/bin/dunit -i "vectorface/php5.3 vectorface/php5.4"
```

Run `dunit` with a custom config file.

```shell
$ ./vendor/bin/dunit -c ../dunit.global.conf
```

## Upgrading from 1.x to 2.x

The `.dunitconfig` syntax has changed entirely from version 1 to version 2 so
it is required that you copy the new config file in place.

```shell
$ cp ./vendor/vectorface/dunit/dunitconfig.example ./.dunitconfig
```

and adjust your config accordingly.
