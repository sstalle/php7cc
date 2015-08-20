<?php

namespace Sstalle\php7cc\Infrastructure;

use Sstalle\php7cc\CLIOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CLIOutputBridge implements CLIOutputInterface
{
    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * {@inheritdoc}
     */
    public function write($string)
    {
        $this->output->write($string);
    }

    /**
     * {@inheritdoc}
     */
    public function writeln($string)
    {
        $this->output->writeln($string);
    }
}
