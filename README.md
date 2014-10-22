#Dunit

[Docker](https://www.docker.com/whatisdocker/) is required for Dunit. Dunit makes your life easier by making sure your PHPUnit tests works across different PHP versions. It also
allows you to check that the syntax passes across different PHP versions as well.

## Installation

Simply the following to your composer.json `require-dev` field like so:

    "require-dev": {
        "vectorface/dunit": "~0.1.0"
    }

Don't forget to run `composer update` afterwards.

## Usage

Once the package is added to require-dev you'll be able to get going started with just this:
```shell
$ ./vendor/bin/dunit
```

#### Basic usage
```shell
#Grab the help docs
$ ./vendor/bin/dunit -h

#Custom configuration files
$ ./vendor/bin/dunit -c "path/.dunit.conf"

#Quickly limit PHP versions to try
$ ./vendor/bin/dunit -p "5.3 5.4"
```

##Configuration
Everything is pretty much customization through the `.dunit.config` file, lets begin by copying the defaults so that you can change them:

```shell
$ cp .dunit.config.dist .dunit.config
#Global environment variables will also work of using .dunit.config
```

#### I only want this to check PHP 5.3 & 5.4

```source
DUNIT_PHPVERSION="5.3 5.4"
```

#### I don't have unit tests & just want syntax checking
```source
DUNIT_SYNTAXONLY=true
```

#### I want to change the PHPUnit path
```source
DUNIT_PHPUNITCOMMAND="/opt/source/vendor/bin/phpunit"
```

#### I need to custom the grep for the syntax checker
```source
DUNIT_PHPSYNTAXCOMMAND="find /opt/source/ -name '*.php' -print0 | xargs -0 -n1 -P8 php -l | grep -i 'on line'"
```

#### Can this do both unit tests checks and syntax checks?
You can bet your bitcoins that it can! Just append `DUNIT_PHPUNITCOMMAND` to `DUNIT_PHPSYNTAXCOMMAND` or vice versa depending on your `DUNIT_SYNTAXONLY` settings.