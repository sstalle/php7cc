<?php

namespace Sstalle\php7cc;

use Sstalle\php7cc\CompatibilityViolation\CheckMetadata;
use Sstalle\php7cc\CompatibilityViolation\ContextInterface;

interface ResultPrinterInterface
{

    /**
     * @param ContextInterface $context
     */
    public function printContext(ContextInterface $context);

    /**
     * @param CheckMetadata $metadata
     */
    public function printMetadata(CheckMetadata $metadata);

}