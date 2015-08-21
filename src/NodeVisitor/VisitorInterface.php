<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\NodeVisitor;
use Sstalle\php7cc\CompatibilityViolation\ContextInterface;

interface VisitorInterface extends NodeVisitor
{
    /**
     * @param ContextInterface $context
     */
    public function initializeContext(ContextInterface $context);

    /**
     * @param array $tokens
     */
    public function setTokens(array $tokens);
}
