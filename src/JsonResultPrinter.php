<?php

namespace Sstalle\php7cc;

use Bcn\Component\Json\Writer;
use Sstalle\php7cc\CompatibilityViolation\CheckMetadata;
use Sstalle\php7cc\CompatibilityViolation\ContextInterface;

class JsonResultPrinter implements ResultPrinterInterface
{
    /**
     * @var CLIOutputInterface
     */
    private $output;
    /**
     * @var Writer
     */
    private $writer;
    /**
     * @var resource
     */
    private $json_file;

    public function __construct(CLIOutputInterface $output)
    {
        $this->output = $output;
        $this->json_file = fopen('php://temp', 'wb');
        $this->writer = new Writer($this->json_file);
        $this->writer->enter(Writer::TYPE_OBJECT);
        $this->writer->enter('files', Writer::TYPE_ARRAY);
    }

    /**
     * @param ContextInterface $context
     */
    public function printContext(ContextInterface $context)
    {
        $resource = $context->getCheckedResourceName();

        $extractMessageDataCallback = $this->buildCallback();

        $this->writer->write(null, array(
            'name' => $resource,
            'errors' => array_map($extractMessageDataCallback, $context->getErrors()),
            'messages' => array_map($extractMessageDataCallback, $context->getMessages()),
        ));
    }
    /**
     * @param CheckMetadata $metadata
     */
    public function printMetadata(CheckMetadata $metadata)
    {
        $this->writer->leave();
        $this->writer->enter('summary', Writer::TYPE_OBJECT);
        $this->writer->write('checkedFiles', $metadata->getCheckedFileCount());
        $this->writer->write('elapsedTime', $metadata->getElapsedTime());
        $this->writer->leave();
        $this->writer->leave();

        rewind($this->json_file);
        $this->output->writeln(stream_get_contents($this->json_file));
    }

    /**
     * @return \Closure
     */
    private function buildCallback()
    {
        return function (AbstractBaseMessage $m) {
            return array('line' => $m->getLine(), 'text' => $m->getRawText());
        };
    }

    public function __destruct()
    {
        fclose($this->json_file);
    }
}
