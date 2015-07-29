<?php

namespace Sstalle\php7cc\Infrastructure;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PHP7CCCommand extends Command
{

    const COMMAND_NAME = 'php7cc';

    const PATH_ARGUMENT_NAME = 'path';
    const EXTENSIONS_ARGUMENT_NAME = 'extensions';

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName(static::COMMAND_NAME)
            ->setDescription('Checks PHP 5.3 - 5.6 code for compatibility with PHP7')
            ->addArgument(
                static::PATH_ARGUMENT_NAME,
                InputArgument::REQUIRED,
                'Which file or directory do you want to check?'
            )->addArgument(
                static::EXTENSIONS_ARGUMENT_NAME,
                InputArgument::OPTIONAL,
                'Which file extensions do you want to check (separate multiple extensions with commas)?',
                'php'
            );
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument(static::PATH_ARGUMENT_NAME);
        if (!is_file($path) && !is_dir($path)) {
            $output->writeln(sprintf('Path %s must be a file or a directory', $path));
            return;
        }

        $extensionsArgumentValue = $input->getArgument(static::EXTENSIONS_ARGUMENT_NAME);
        $extensions = explode(',', $extensionsArgumentValue);
        if (!is_array($extensions)) {
            $output->writeln(
                sprintf(
                    'Something went wrong while parsing file extensions you specified. ' .
                    'Check that %s is a comma-separated list of extensions',
                    $extensionsArgumentValue
                )
            );

            return;
        }


        $containerBuilder = new ContainerBuilder();
        $container = $containerBuilder->buildContainer($output);

        $container['pathChecker']->check($path, $extensions);
    }

}