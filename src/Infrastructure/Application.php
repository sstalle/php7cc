<?php

namespace Sstalle\php7cc\Infrastructure;

use Symfony\Component\Console\Input\InputInterface;

class Application extends \Symfony\Component\Console\Application
{
    const VERSION = '1.2.1';

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct('PHP 7 Compatibility Checker', static::VERSION);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        $inputDefinition = parent::getDefinition();
        $inputDefinition->setArguments();

        return $inputDefinition;
    }

    /**
     * {@inheritdoc}
     */
    protected function getCommandName(InputInterface $input)
    {
        return PHP7CCCommand::COMMAND_NAME;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultCommands()
    {
        $defaultCommands = parent::getDefaultCommands();
        $defaultCommands[] = new PHP7CCCommand();

        return $defaultCommands;
    }
}
