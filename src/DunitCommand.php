<?php

namespace Vectorface\Dunit;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DunitCommand extends Command
{

    const COMMAND_NAME = 'dunit';
    const COMMAND_DESCRIPTION = 'Run PHP syntax and unit tests against multiple docker containers.';

    const ENV_VAR_IMAGES = 'DUNIT_IMAGES';
    const ENV_VAR_SYNTAX = 'DUNIT_PHPSYNTAX';
    const ENV_VAR_SYNTAXCOMMAND = 'DUNIT_PHPSYNTAXCOMMAND';
    const ENV_VAR_PHPUNIT = 'DUNIT_PHPUNIT';
    const ENV_VAR_PHPUNITCOMMAND = 'DUNIT_PHPUNITCOMMAND';

    const OPTIONS_KEY_IMAGES = 'images';
    const OPTIONS_KEY_CONFIGFILE = 'configFile';
    const OPTIONS_KEY_SYNTAX = 'checkSyntax';
    const OPTIONS_KEY_SYNTAXCOMMAND = 'syntaxCommand';
    const OPTIONS_KEY_UNITTESTS = 'runTests';
    const OPTIONS_KEY_UNITTESTSCOMMAND = 'unitTestsCommand';

    protected function configure()
    {
        $this->setName(
            self::COMMAND_NAME
        )->setDescription(
            self::COMMAND_DESCRIPTION
        );
        $this->configureOptions();
    }

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
    }

    private function getDefaultOptions($configFile, OutputInterface $output)
    {
        $defaultOptions = array(
            self::OPTIONS_KEY_IMAGES => array(
                'vectorface\php5.4',
                'vectorface\php5.5',
                'vectorface\php5.6',
                'vectorface\hhvm'
            ),
            self::OPTIONS_KEY_SYNTAX => true,
            self::OPTIONS_KEY_SYNTAXCOMMAND => 'find /opt/source -type f -name "*.php" !  -path "*/vendor/*" -print0 | xargs -0 -n 1 -P 8 php -l | grep -v "No syntax errors"',
            self::OPTIONS_KEY_UNITTESTS => true,
            self::OPTIONS_KEY_UNITTESTSCOMMAND => '/opt/source/vendor/bin/phpunit'
        );

        // merge in the config file
        $defaultOptions = array_merge(
            $defaultOptions,
            $this->importConfigFile($configFile, $output)
        );
        // map environment variables to script options
        $optionsMap = array(
            self::ENV_VAR_IMAGES => self::OPTIONS_KEY_IMAGES,
            self::ENV_VAR_SYNTAX => self::OPTIONS_KEY_SYNTAX,
            self::ENV_VAR_SYNTAXCOMMAND => self::OPTIONS_KEY_SYNTAXCOMMAND,
            self::ENV_VAR_PHPUNIT => self::OPTIONS_KEY_UNITTESTS,
            self::ENV_VAR_PHPUNITCOMMAND => self::OPTIONS_KEY_UNITTESTSCOMMAND
        );
        foreach ($optionsMap as $env => $option) {
            if (($value = getenv($env))) {
                $defaultOptions[$option] = $value;
            }
        }

        // convert any images string into an array
        if (is_string($defaultOptions[self::OPTIONS_KEY_IMAGES])) {
            $defaultOptions[self::OPTIONS_KEY_IMAGES] = $this->parseImagesStringToArray(
                $defaultOptions[self::OPTIONS_KEY_IMAGES]
            );
        }
        $defaultOptions[self::OPTIONS_KEY_SYNTAX] = boolval($defaultOptions[self::OPTIONS_KEY_SYNTAX]);
        $defaultOptions[self::OPTIONS_KEY_UNITTESTS] = (bool)$defaultOptions[self::OPTIONS_KEY_UNITTESTS];

        return $defaultOptions;
    }

    private function importConfigFile($configFile, OutputInterface $output)
    {
        $configFile = ($configFile) ?: realpath(getcwd()).'/.dunitconfig';
        if (file_exists($configFile) && is_readable($configFile)) {
            return parse_ini_file($configFile);
        }
        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
            $output->writeln('WARNING: Config file not found. Falling back to built-in defaults.');
        }
        return array();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $options = $this->getCurrentOptions($input, $output);
        $output->writeln(print_r($options, true));
    }

    private function getCurrentOptions(InputInterface $input, OutputInterface $output)
    {
        $options = $this->getDefaultOptions(
            $input->getOption('configFile'),
            $output
        );
        $options['images'] = $this->getImagesOption($input, $options);
        return $options;
    }

    private function getImagesOption(InputInterface $input, $defaultOptions)
    {
        $images = $input->getOption('images');
        if ($images) {
            $images = $this->parseImagesStringToArray($images);
            if (!empty($images)) {
                return $images;
            }
        }
        return $defaultOptions['images'];
    }

    private function parseImagesStringToArray($imagesString)
    {
        return array_values(
            array_filter(
                array_map(
                    'trim',
                    explode(
                        ' ',
                        str_replace(',', ' ', $imagesString)
                    )
                ),
                'strlen'
            )
        );
    }

    /**
     * Returns the help for the command.
     * @return string The help for the command.
     */
    public function getHelp()
    {
        return 'hello world';
    }
}

