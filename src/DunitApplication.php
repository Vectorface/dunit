<?php

namespace Vectorface\Dunit;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;

/**
 * To build a single command application the Symfony cookbook recommends
 * subclassing the Console\Application class and overriding specific methods to
 * invoke a specific command by default.
 * @author Daniel Bruce <dbruce@vectorface.com>
 * @copyright Vectorface, Inc. 2014
 */
class DunitApplication extends Application
{
    /** the default command we want to run */
    private $command;

    /** The current version string */
    const CURRENT_VERSION = "dev";

    /**
     * Returns an instance of the default command.
     * @return DunitCommand An instance of the default command.
     */
    private function getCommand()
    {
        if (null === $this->command) {
            $this->command = new DunitCommand();
        }
        return $this->command;
    }

    /**
     * Gets the name of the command based on input. In this case we return the
     * name of the default command.
     * @param InputInterface $input The input interface.
     * @return string The name of the command.
     */
    protected function getCommandName(InputInterface $input)
    {
        return $this->getCommand()->getName();
    }

    /**
     * Gets the default commands that should always be available.
     * @return array An array of default Command instances.
     */
    protected function getDefaultCommands()
    {
        $defaultCommands = parent::getDefaultCommands();
        $defaultCommands[] = $this->getCommand();
        return $defaultCommands;
    }

    /**
     * Overridden so that the application doesn't expect the command name to be
     * the first argument.
     * @return \Symfony\Component\Console\Input\InputDefinition Returns an
     * instance of the input definition.
     */
    public function getDefinition()
    {
        $inputDefinition = parent::getDefinition();
        $inputDefinition->setArguments();
        return $inputDefinition;
    }

    /**
     * Returns the current (short) version of the application.
     * @return string The current version of the application.
     */
    public function getVersion()
    {
        return self::CURRENT_VERSION;
    }

    /**
     * Returns the full version of the application.
     * @return string The full version of the application.
     */
    public function getLongVersion()
    {
        return sprintf('dunit-%s', self::CURRENT_VERSION);
    }
}

