<?php

namespace Sstalle\php7cc;

use Sstalle\php7cc\CompatibilityViolation\CheckMetadata;
use Sstalle\php7cc\CompatibilityViolation\ContextInterface;

interface ResultPrinterInterface
{
    const PLAIN_FORMAT = 'plain';
    const JSON_FORMAT = 'json';

    /**
     * @param ContextInterface $context
     */
    public function printContext(ContextInterface $context);

    /**
     * @param CheckMetadata $metadata
     */
    public function printMetadata(CheckMetadata $metadata);
}
