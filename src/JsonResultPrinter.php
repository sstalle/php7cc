<?php

namespace Sstalle\php7cc;

use Sstalle\php7cc\CompatibilityViolation\CheckMetadata;
use Sstalle\php7cc\CompatibilityViolation\ContextInterface;

class JsonResultPrinter implements ResultPrinterInterface
{
    /**
     * @var array $resultData json output content and structure
     */
    private $resultData;
    /**
     * @var CLIOutputInterface
     */
    private $output;

    public function __construct(CLIOutputInterface $output)
    {
        $this->output = $output;
        $this->resultData = array();
    }

    /**
     * @param ContextInterface $context
     */
    public function printContext(ContextInterface $context)
    {
        $resource = $context->getCheckedResourceName();

        $extractMessageDataCallback = $this->buildCallback();

        $this->resultData[$resource] = array(
            'errors' => array_map($extractMessageDataCallback, $context->getErrors()),
            'messages' => array_map($extractMessageDataCallback, $context->getMessages())
        );
    }
    /**
     * @param CheckMetadata $metadata
     */
    public function printMetadata(CheckMetadata $metadata)
    {
        $this->output->writeln(json_encode($this->resultData, JSON_PRETTY_PRINT));
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
}
