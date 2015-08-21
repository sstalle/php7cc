<?php

namespace Sstalle\php7cc\Infrastructure;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PHP7CCCommand extends Command
{
    const COMMAND_NAME = 'php7cc';

    const PATHS_ARGUMENT_NAME = 'paths';
    const EXTENSIONS_OPTION_NAME = 'extensions';
    const EXCEPT_OPTION_NAME = 'except';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName(static::COMMAND_NAME)
            ->setDescription('Checks PHP 5.3 - 5.6 code for compatibility with PHP7')
            ->addArgument(
                static::PATHS_ARGUMENT_NAME,
                InputArgument::REQUIRED | InputArgument::IS_ARRAY,
                'Which file or directory do you want to check?'
            )->addOption(
                static::EXTENSIONS_OPTION_NAME,
                'e',
                InputOption::VALUE_OPTIONAL,
                'Which file extensions do you want to check (separate multiple extensions with commas)?',
                'php'
            )->addOption(
                static::EXCEPT_OPTION_NAME,
                'x',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Excluded files and directories',
                array()
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $paths = $input->getArgument(static::PATHS_ARGUMENT_NAME);
        foreach ($paths as $path) {
            if (!is_file($path) && !is_dir($path)) {
                $output->writeln(sprintf('Path %s must be a file or a directory', $path));

                return;
            }
        }

        $extensionsArgumentValue = $input->getOption(static::EXTENSIONS_OPTION_NAME);
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

        $container['pathCheckExecutor']->check($paths, $extensions, $input->getOption(static::EXCEPT_OPTION_NAME));
    }
}
