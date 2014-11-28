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

    protected function configure()
    {
        $this->setName(
            self::COMMAND_NAME
        )->setDescription(
            self::COMMAND_DESCRIPTION
        );
        $this->configureOptions();
    }

    private function getDefaultOptions($configFile)
    {
        $defaultOptions = array(
            'images' => array(
                'vectorface\php5.4',
                'vectorface\php5.5',
                'vectorface\php5.6',
                'vectorface\hhvm'
            )
        );

        // merge in the config file
        $defaultOptions = array_merge(
            $defaultOptions,
            $this->importConfigFile($configFile)
        );

        // override any of the options with environment variables
        if (($imagesEnv = getenv('DUNIT_IMAGES'))) {
            $defaultOptions['images'] = $imagesEnv;
        }

        // convert any images string into an array
        if (is_string($defaultOptions['images'])) {
            $defaultOptions['images'] = $this->parseImagesStringToArray(
                $defaultOptions['images']
            );
        }

        return $defaultOptions;
    }

    private function configureOptions()
    {
        $this->addOption(
            'images',
            'i',
            InputOption::VALUE_OPTIONAL,
            'The list of docker images to use (comma or space delimited).'
        );
        $this->addOption(
            'configFile',
            'c',
            InputOption::VALUE_OPTIONAL,
            'The path to the configuration file.'
        );
    }

    private function importConfigFile($configFile)
    {
        $configFile = ($configFile) ?: realpath(getcwd()).'/.dunitconfig';
        if (file_exists($configFile) && is_readable($configFile)) {
            return parse_ini_file($configFile);
        }
        return array();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $options = $this->getCurrentOptions($input);
        $output->writeln(print_r($options, true));
    }

    private function getCurrentOptions(InputInterface $input)
    {
        $options = $this->getDefaultOptions($input->getOption('configFile'));
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

