<?php

namespace Sstalle\php7cc\Infrastructure;

use Symfony\Component\Console\Input\InputInterface;

class Application extends \Symfony\Component\Console\Application
{

    /**
     * @inheritDoc
     */
    public function getDefinition()
    {
        $inputDefinition = parent::getDefinition();
        $inputDefinition->setArguments();

        return $inputDefinition;
    }

    /**
     * @inheritDoc
     */
    protected function getCommandName(InputInterface $input)
    {
        return PHP7CCCommand::COMMAND_NAME;
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultCommands()
    {
        $defaultCommands = parent::getDefaultCommands();
        $defaultCommands[] = new PHP7CCCommand();

        return $defaultCommands;
    }

}