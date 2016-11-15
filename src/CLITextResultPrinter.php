<?php

namespace Sstalle\php7cc;

use Sstalle\php7cc\CompatibilityViolation\CheckMetadata;
use Sstalle\php7cc\CompatibilityViolation\ContextInterface;
use Sstalle\php7cc\CompatibilityViolation\Message;

class CLITextResultPrinter implements ResultPrinterInterface
{
    /**
     * @var array
     */
    private static $colors = array(
        AbstractBaseMessage::LEVEL_INFO => null,
        AbstractBaseMessage::LEVEL_WARNING => 'yellow',
        AbstractBaseMessage::LEVEL_ERROR => 'red',
    );

    /**
     * @var CLIOutputInterface
     */
    protected $output;

    /**
     * @var NodePrinterInterface
     */
    protected $nodePrinter;

    /**
     * @param CLIOutputInterface   $output
     * @param NodePrinterInterface $nodePrinter
     */
    public function __construct(CLIOutputInterface $output, NodePrinterInterface $nodePrinter)
    {
        $this->output = $output;
        $this->nodePrinter = $nodePrinter;
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
        $prettyPrintedNodes = $this->nodePrinter->printNodes($message->getNodes());

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
    }
}
