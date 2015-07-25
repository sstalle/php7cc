<?php

namespace Sstalle\php7cc;

use PhpParser\PrettyPrinter\Standard as StandardPrettyPrinter;
use Sstalle\php7cc\CompatibilityViolation\CheckMetadata;
use Sstalle\php7cc\CompatibilityViolation\ContextInterface;

class CLIResultPrinter implements ResultPrinterInterface
{

    /**
     * @var CLIOutputInterface
     */
    protected $output;

    /**
     * @var StandardPrettyPrinter
     */
    protected $prettyPrinter;

    /**
     * @var NodeStatementsRemover
     */
    protected $nodeStatementsRemover;

    /**
     * @param CLIOutputInterface $output
     * @param StandardPrettyPrinter $prettyPrinter
     * @param NodeStatementsRemover $nodeStatementsRemover
     */
    public function __construct(
        CLIOutputInterface $output,
        StandardPrettyPrinter $prettyPrinter,
        NodeStatementsRemover $nodeStatementsRemover
    ) {
        $this->output = $output;
        $this->prettyPrinter = $prettyPrinter;
        $this->nodeStatementsRemover = $nodeStatementsRemover;
    }

    /**
     * @inheritDoc
     */
    public function printContext(ContextInterface $context)
    {
        $this->output->writeln('');
        $this->output->writeln(sprintf('File: %s', $context->getCheckedResourceName()));

        foreach ($context->getMessages() as $message) {
            $nodes = $this->nodeStatementsRemover->removeInnerStatements($message->getNodes());

            $this->output->writeln(
                sprintf(
                    'Line %d. %s: %s',
                    $message->getLine(),
                    $message->getText(),
                    $this->prettyPrinter->prettyPrint($nodes)
                )
            );
        }

        $this->output->writeln('');
    }

    /**
     * @inheritDoc
     */
    public function printMetadata(CheckMetadata $metadata)
    {
        $this->output->writeln(
            sprintf(
                'Checked %d file(s) in %f second(s)',
                $metadata->getCheckedFileCount(),
                $metadata->getElapsedTime()
            )
        );
    }

}