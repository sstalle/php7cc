<?php

namespace Sstalle\php7cc;

use PhpParser\PrettyPrinter\Standard as StandardPrettyPrinter;
use Sstalle\php7cc\CompatibilityViolation\CheckMetadata;
use Sstalle\php7cc\CompatibilityViolation\ContextInterface;
use Sstalle\php7cc\CompatibilityViolation\Message;

class CLIResultPrinter implements ResultPrinterInterface
{
    private static $colors = array(
        Message::LEVEL_INFO => null,
        Message::LEVEL_WARNING => 'yellow',
        Message::LEVEL_ERROR => 'red',
    );

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
            $this->output->writeln(
                $this->formatMessage($message)
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

    /**
     * @param Message $message
     *
     * @return string
     */
    private function formatMessage(Message $message)
    {
        $nodes = $this->nodeStatementsRemover->removeInnerStatements($message->getNodes());
        $prettyPrintedNodes = str_replace("\n", "\n    ", $this->prettyPrinter->prettyPrint($nodes));

        $text = $message->getRawText();
        $color = self::$colors[$message->getLevel()];

        if ($color) {
            $text = sprintf(
                '<fg=%s>%s</fg=%s>',
                $color,
                $text,
                $color
            );
        }

        return sprintf(
            "> Line <fg=cyan>%s</fg=cyan>: %s\n    %s",
            $message->getLine(),
            $text,
            $prettyPrintedNodes
        );

        return $string;
    }
}
