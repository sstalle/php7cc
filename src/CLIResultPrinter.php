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
     * @param CLIOutputInterface    $output
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
     * {@inheritdoc}
     */
    public function printContext(ContextInterface $context)
    {
        $this->output->writeln('');
        $this->output->writeln(sprintf('File: <fg=cyan>%s</fg=cyan>', $context->getCheckedResourceName()));

        foreach ($context->getMessages() as $message) {
            $nodes = $this->nodeStatementsRemover->removeInnerStatements($message->getNodes());

            $this->output->writeln(
                sprintf(
                    "> Line <fg=cyan>%s</fg=cyan>: <fg=yellow>%s</fg=yellow>\n    %s",
                    $message->getLine(),
                    $message->getRawText(),
                    str_replace("\n", "\n    ", $this->prettyPrinter->prettyPrint($nodes))
                )
            );
        }

        foreach ($context->getErrors() as $error) {
            $this->output->writeln(
                sprintf(
                    '> <fg=red>%s</fg=red>',
                    $error->getText()
                )
            );
        }

        $this->output->writeln('');
    }

    /**
     * {@inheritdoc}
     */
    public function printMetadata(CheckMetadata $metadata)
    {
        $checkedFileCount = $metadata->getCheckedFileCount();
        $elapsedTime = $metadata->getElapsedTime();

        $this->output->writeln(
            sprintf(
                'Checked <fg=green>%d</fg=green> file%s in <fg=green>%.3f</fg=green> second%s',
                $checkedFileCount,
                $checkedFileCount > 1 ? 's' : '',
                $elapsedTime,
                $elapsedTime > 1 ? 's' : ''
            )
        );
    }
}
