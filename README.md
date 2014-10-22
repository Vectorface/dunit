#Dunit

Dunit uses docker containers so that your tests can be ran against an
array of PHP versions.

## Installation

Simply the following to your composer.json `require-dev` field like so:

    "require-dev": {
        "vectorface/dunit": "~0.1.0"
    }

Don't forget to run `composer update` to get dunit.

## Usage

```shell
$ ./vendor/bin/dunit
```