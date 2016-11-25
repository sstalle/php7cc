<?php

namespace Sstalle\php7cc;

use Bcn\Component\Json\Writer;
use Sstalle\php7cc\CompatibilityViolation\CheckMetadata;
use Sstalle\php7cc\CompatibilityViolation\ContextInterface;
use Sstalle\php7cc\Infrastructure\CLIOutputBridge;

class CLIJSONResultPrinter implements ResultPrinterInterface
{
    /**
     * @var CLIOutputBridge
     */
    protected $output;

    /**
     * @var NodePrinterInterface
     */
    protected $nodePrinter;

    /**
     * @var Writer
     */
    protected $writer;

    /**
     * @var bool
     */
    protected $isInsideTopContext = false;

    /**
     * @var bool
     */
    protected $isInsideErrorContext = false;

    /**
     * @param CLIOutputBridge      $output
     * @param NodePrinterInterface $nodePrinter
     * @param Writer               $writer
     */
    public function __construct(CLIOutputBridge $output, NodePrinterInterface $nodePrinter, Writer $writer)
    {
        $this->output = $output;
        $this->nodePrinter = $nodePrinter;
        $this->writer = $writer;
    }

    /**
     * {@inheritdoc}
     */
    public function printContext(ContextInterface $context)
    {
        $this->openTopContext();
        $this->openErrorsContext();

        foreach ($context->getMessages() as $message) {
            $prettyPrintedNodes = $this->nodePrinter->printNodes($message->getNodes());

            $this->writer->write(null, array(
                'file' => $context->getCheckedResourceName(),
                'level' => $message->getLevelName(),
                'line' => $message->getLine(),
                'text' => $message->getRawText(),
                'code' => $prettyPrintedNodes,
            ));
        }

        foreach ($context->getErrors() as $error) {
            $this->writer->write(null, array(
                'level' => $error->getLevelName(),
                'file' => $context->getCheckedResourceName(),
                'line' => $error->getLine(),
                'text' => $error->getRawText(),
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function printMetadata(CheckMetadata $metadata)
    {
        $this->openTopContext();
        $this->openErrorsContext();

        $this->writer->leave();

        $this->writer->write('filesChecked', $metadata->getCheckedFileCount());
        $this->writer->write('elapsedTime', $metadata->getElapsedTime());
        $this->writer->leave();

        $this->output->writeln('');
    }

    protected function openTopContext()
    {
        if ($this->isInsideTopContext) {
            return;
        }

        $this->writer->enter(Writer::TYPE_OBJECT);
        $this->isInsideTopContext = true;
    }

    protected function openErrorsContext()
    {
        if ($this->isInsideErrorContext) {
            return;
        }

        $this->writer->enter('errors', Writer::TYPE_ARRAY);
        $this->isInsideErrorContext = true;
    }
}
