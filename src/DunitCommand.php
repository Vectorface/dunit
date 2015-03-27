<?php

namespace Vectorface\Dunit;

use \Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The main "dunit" command.
 * @author Daniel Bruce <dbruce@vectorface.com>
 * @copyright VectorFace, Inc. 2014
 */
class DunitCommand extends Command
{
    /** The name of the command for Symfony */
    const COMMAND_NAME = 'dunit';
    /** A description of the command for Symfony */
    const COMMAND_DESCRIPTION = 'Run PHP syntax and unit tests against multiple docker containers.';

    /** The environment variable storing the list of docker images */
    const ENV_VAR_IMAGES = 'DUNIT_IMAGES';
    /**
     * The environment variable storing the flag as to whether we should run
     * the syntax check
     */
    const ENV_VAR_SYNTAX = 'DUNIT_PHPSYNTAX';
    /** The environment variable storing the syntax check command */
    const ENV_VAR_SYNTAXCOMMAND = 'DUNIT_PHPSYNTAXCOMMAND';
    /**
     * The environment variable storing the flag as to whether we should run
     * the unit test suite
     */
    const ENV_VAR_PHPUNIT = 'DUNIT_PHPUNIT';
    /** The environment variable storing the unit test command */
    const ENV_VAR_PHPUNITCOMMAND = 'DUNIT_PHPUNITCOMMAND';

    /** The option storing the config file path */
    const OPTIONS_KEY_CONFIGFILE = 'configFile';
    /** The option for the list of images */
    const OPTIONS_KEY_IMAGES = 'images';
    /** The flag indicating whether we should check syntax */
    const OPTIONS_KEY_SYNTAX = 'checkSyntax';
    /** The option for the syntax command */
    const OPTIONS_KEY_SYNTAXCOMMAND = 'syntaxCommand';
    /** The flag indicating whether we should run unit tests */
    const OPTIONS_KEY_UNITTESTS = 'runTests';
    /** The option for the unit tests command */
    const OPTIONS_KEY_UNITTESTSCOMMAND = 'unitTestsCommand';
    /** The option key to pull new images instead of running the commands. */
    const OPTIONS_KEY_PULL = 'pull';

    /** Exit code for an error */
    const ERROR_CODE = 1;
    /** Exit code for successful execution */
    const NO_ERROR = 0;

    /** The default width if we are unable to dynamically determine the
      * current terminal width */
    const DEFAULT_WIDTH = 80;
    /** The array key for the terminal width */
    const DIMENSION_WIDTH = 0;
    /** The array key for the terminal height */
    const DIMENSION_HEIGHT = 1;

    /** The format of the docker command that we execute */
    const DOCKER_COMMAND_FORMAT = 'docker run -t -v $(pwd):/opt/source -w /opt/source %s /bin/bash -c " stty columns %d && %s "';

    /** The format of the docker pull command for updating images */
    const DOCKER_COMMAND_PULL = 'docker pull %s';

    // a map from environment variables to parameter options
    private static $optionsMap = array(
        self::ENV_VAR_IMAGES => self::OPTIONS_KEY_IMAGES,
        self::ENV_VAR_SYNTAX => self::OPTIONS_KEY_SYNTAX,
        self::ENV_VAR_SYNTAXCOMMAND => self::OPTIONS_KEY_SYNTAXCOMMAND,
        self::ENV_VAR_PHPUNIT => self::OPTIONS_KEY_UNITTESTS,
        self::ENV_VAR_PHPUNITCOMMAND => self::OPTIONS_KEY_UNITTESTSCOMMAND
    );

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        parent::configure();
        $this->setName(
            self::COMMAND_NAME
        )->setDescription(
            self::COMMAND_DESCRIPTION
        );
        $this->configureOptions();
    }

    /**
     * Configures the list of command options.
     */
    private function configureOptions()
    {
        $this->addOption(
            self::OPTIONS_KEY_IMAGES,
            'i',
            InputOption::VALUE_OPTIONAL,
            'The list of docker images to use (comma or space delimited).'
        );
        $this->addOption(
            self::OPTIONS_KEY_CONFIGFILE,
            'c',
            InputOption::VALUE_OPTIONAL,
            'The path to the configuration file.'
        );
        $this->addOption(
            self::OPTIONS_KEY_SYNTAX,
            's',
            InputOption::VALUE_OPTIONAL,
            'A flag indicating whether to run syntax checks.'
        );
        $this->addOption(
            self::OPTIONS_KEY_SYNTAXCOMMAND,
            'S',
            InputOption::VALUE_OPTIONAL,
            'The command to run syntax checks.'
        );
        $this->addOption(
            self::OPTIONS_KEY_UNITTESTS,
            'u',
            InputOption::VALUE_OPTIONAL,
            'A flag indicating whether to run the unit test suite.'
        );
        $this->addOption(
            self::OPTIONS_KEY_UNITTESTSCOMMAND,
            'U',
            InputOption::VALUE_OPTIONAL,
            'The command to run the unit tests.'
        );
        $this->addOption(
            self::OPTIONS_KEY_PULL,
            'p',
            InputOption::VALUE_NONE,
            'A flag indicating to pull new images instead of running the linter or unit tests.'
        );
    }

    /**
     * The entry point for the command.
     * @param InputInterface $input The input for options and arguments.
     * @param OutputInterface $output The output writer.
     * @return null|int null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // retrieve the current settings
        $options = $this->getCurrentOptions($input, $output);

        // check if docker is installed
        if (false === $this->commandExists('docker')) {
            $output->writeln('Docker is required to run DUnit.');
            return self::ERROR_CODE;
        }
        // loop over each of the docker images specified
        foreach ($options[self::OPTIONS_KEY_IMAGES] as $image) {
            if ($options[self::OPTIONS_KEY_PULL]) {
                passthru(sprintf(
                    self::DOCKER_COMMAND_PULL,
                    escapeshellarg($image)
                ));
                continue;
            }

            $output->writeln('Running against image '.$image);
            if ($options[self::OPTIONS_KEY_SYNTAX]) {
                $output->writeln('Checking syntax');
                // run the syntax checks
                $this->executeCommandForImage(
                    $image,
                    $options[self::OPTIONS_KEY_SYNTAXCOMMAND]
                );
            }
            if ($options[self::OPTIONS_KEY_UNITTESTS]) {
                $output->writeln('Running unit test suite');
                // run the unit tests
                $this->executeCommandForImage(
                    $image,
                    $options[self::OPTIONS_KEY_UNITTESTSCOMMAND]
                );
            }
        }
        return self::NO_ERROR;
    }

    /**
     * Returns an array of the currently active options given all possible
     * options, environment variables, config file settings, etc.
     * @param InputInterface $input The input of options and arguments.
     * @param OutputInterface $output The output writer.
     * @return array Returns an array of settings.
     */
    private function getCurrentOptions(InputInterface $input, OutputInterface $output)
    {
        $options = $this->getDefaultOptions();
        try {
            $options = $this->mergeConfigFileIntoOptions(
                $options,
                $input->getOption(self::OPTIONS_KEY_CONFIGFILE)
            );
        } catch (Exception $e) {
            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
                $output->writeln('WARNING: Config file could not be read. Falling back to built-in defaults.');
            }
        }
        $options = $this->mergeEnvironmentVariablesIntoOptions($options);

        // handle the individual options
        // handle the list of images
        $options[self::OPTIONS_KEY_IMAGES] = $this->getArrayOption(
            $input, self::OPTIONS_KEY_IMAGES, $options[self::OPTIONS_KEY_IMAGES]
        );

        // if the user specified a syntax command we assume they want to run
        // the syntax checks
        $syntaxCommand = $input->getOption(self::OPTIONS_KEY_SYNTAXCOMMAND);
        if (!empty($syntaxCommand)) {
            $options[self::OPTIONS_KEY_SYNTAX] = true;
        } else {
            // check for the syntax flag
            $options[self::OPTIONS_KEY_SYNTAX] = $this->getBooleanOption(
                $input, self::OPTIONS_KEY_SYNTAX, $options[self::OPTIONS_KEY_SYNTAX]
            );
        }
        // if they are running syntax, check for the actual command option
        if ($options[self::OPTIONS_KEY_SYNTAX]) {
            $options[self::OPTIONS_KEY_SYNTAXCOMMAND] = $this->getStringOption(
                $input, self::OPTIONS_KEY_SYNTAXCOMMAND, $options[self::OPTIONS_KEY_SYNTAXCOMMAND]
            );
        }

        // if the user specified a unit tests command we assume they want to
        // run the unit tests
        if ($input->hasOption(self::OPTIONS_KEY_UNITTESTSCOMMAND)) {
            $options[self::OPTIONS_KEY_UNITTESTS] = true;
        } else {
            // check for the unit tests flag
            $options[self::OPTIONS_KEY_UNITTESTS] = $this->getBooleanOption(
                $input, self::OPTIONS_KEY_UNITTESTS, $options[self::OPTIONS_KEY_UNITTESTS]
            );
        }
        // if they are running the unit tests, check for the actual command
        if ($options[self::OPTIONS_KEY_UNITTESTS]) {
            $options[self::OPTIONS_KEY_UNITTESTSCOMMAND] = $this->getStringOption(
                $input,
                self::OPTIONS_KEY_UNITTESTSCOMMAND,
                $options[self::OPTIONS_KEY_UNITTESTSCOMMAND]
            );
        }

        $options[self::OPTIONS_KEY_PULL] = $input->getOption(self::OPTIONS_KEY_PULL);

        return $options;
    }

    /**
     * Returns an array of built-in options if none are found or specified.
     * @return array The array of deafult options.
     */
    private function getDefaultOptions()
    {
        // the list of default options if no config is found or specified
        return array(
            self::OPTIONS_KEY_IMAGES => array(
                'vectorface/php5.4',
                'vectorface/php5.5',
                'vectorface/php5.6',
                'vectorface/hhvm'
            ),
            self::OPTIONS_KEY_SYNTAX => true,
            self::OPTIONS_KEY_SYNTAXCOMMAND => 'find /opt/source -type f -name "*.php" !  -path "*/vendor/*" -print0 | xargs -0 -n 1 -P 8 php -l | grep -v "No syntax errors"',
            self::OPTIONS_KEY_UNITTESTS => true,
            self::OPTIONS_KEY_UNITTESTSCOMMAND => '/opt/source/vendor/bin/phpunit',
            self::OPTIONS_KEY_PULL => false,
        );
    }

    /**
     * Merges the default options with values from the config file specified.
     * @param array $options The current options.
     * @param string $configFile The path to the config file. If the config
     *        file specified is not valid the method will fall back to the
     *        default config file location (./.dunitconfig)
     * @return array Returns the array of merged settings.
     * @throws Exception Throws an exception if no config file can be found.
     */
    private function mergeConfigFileIntoOptions($options, $configFile)
    {
        $configFile = ($configFile) ?: realpath(getcwd()).'/.dunitconfig';
        if (file_exists($configFile) && is_readable($configFile)) {
            if (false !== ($settings = parse_ini_file($configFile))) {
                return array_merge($options, $settings);
            }
        }
        throw new Exception(
            'WARNING: Config file could not be read. Falling back to built-in defaults.'
        );
    }

    /**
     * Merges in the value of the environment variables into the settings.
     * @param array $options The current settings.
     * @return array The merged settings with the environment variables.
     */
    private function mergeEnvironmentVariablesIntoOptions($options)
    {
        // map environment variables to script options
        foreach (self::$optionsMap as $env => $option) {
            if (false !== ($value = getenv($env))) {
                $options[$option] = (string)$value;
            }
        }
        return $options;
    }

    /**
     * Retrieves the given key from the input options as an array and uses
     * the default value if not found or empty.
     * @param InputInterface $input The input for options.
     * @param string $key The input key for the option.
     * @param mixed $default current value for the images.
     * @return array Returns the list of images as an array.
     */
    private function getArrayOption(InputInterface $input, $key, $default)
    {
        $value = trim((string)$input->getOption($key));
        if (strlen($value)) {
            $current = $this->parseStringToArray($input->getOption($key));
            if (!empty($current)) {
                return $current;
            }
        }
        return is_string($default) ? $this->parseStringToArray($default) : $default;
    }

    /**
     * Parses a string into an array by splitting on spaces, commas or
     * new line characters and stripping out any empty elements.
     * @param string $string The input string.
     * @return array Returns the list of elements as an array.
     */
    private function parseStringToArray($string)
    {
        // convert commas to spaces
        $string = str_replace(',', ' ', $string);
        // convert new lines to spaces
        $string = str_replace(PHP_EOL, ' ', $string);
        // split on the spaces and filter out empty elements
        return array_values(
            array_filter(
                array_map(
                    'trim',
                    explode(' ', $string)
                ),
                'strlen'
            )
        );
    }

    /**
     * Retrieves the given key from the input options as a boolean.
     * @param InputInterface $input The input interface.
     * @param string $key The option key.
     * @param mixed $default The default value.
     * @return boolean Returns the boolean value of the option or the default
     *         if the option is not found.
     */
    private function getBooleanOption(InputInterface $input, $key, $default)
    {
        $value = trim((string)$input->getOption($key));
        if (strlen($value)) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }
        return filter_var($default, FILTER_VALIDATE_BOOLEAN);
    }


    /**
     * Retrieves the given key from the input options as a string.
     * @param InputInterface $input The input interface.
     * @param string $key The option key.
     * @param mixed $default The default value.
     * @return string Returns the string value of the option or the default if
     *         if the option is not found.
     */
    private function getStringOption(InputInterface $input, $key, $default)
    {
        $value = trim((string)$input->getOption($key));
        if (strlen($value)) {
            return $value;
        }
        return (string)$default;
    }


    /**
     * Returns the help for the command.
     * @return string The help for the command.
     */
    public function getHelp()
    {
        return implode(PHP_EOL, array(
            'The dunit command allows for the execution of PHP syntax checker (linter) and a phpunit test suite against multiple docker images in sequence.'
        ));
    }

    /**
     * Determines if a command exists on the current environment.
     *
     * N.B. This function is not cross platform. The "which" command used is
     * not portable to Microsoft Windows OS. Feel free to provide a pull
     * request if you wish to use dunit on windows.
     *
     * @param string $command The command to check
     * @return boolean Returns true if the command has been found and false,
     *         otherwise.
     */
    private function commandExists($command)
    {
        $response = trim(shell_exec(sprintf(
            'which %s',
            escapeshellarg($command)
        )));
        return !empty($response);
    }

    /**
     * Executes an arbitrary command against the image.
     * @param string $image The docker image to use.
     * @param string $command The command to run inside the docker image.
     */
    private function executeCommandForImage($image, $command)
    {
        passthru(sprintf(
            self::DOCKER_COMMAND_FORMAT,
            escapeshellarg($image),
            intval($this->getColumns()),
            $command
        ));
    }

    /**
     * Returns the number of character columns in the terminal.
     * @return int The width of the terminal (in number of characters).
     */
    private function getColumns()
    {
        $dimensions = $this->getApplication()->getTerminalDimensions();
        if (is_array($dimensions) && count($dimensions) > self::DIMENSION_WIDTH) {
            return $dimensions[self::DIMENSION_WIDTH];
        } else {
            return self::DEFAULT_WIDTH;
        }
    }
}

