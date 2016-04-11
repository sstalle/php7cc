<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\NodeVisitor;
use Sstalle\php7cc\CompatibilityViolation\ContextInterface;
use Sstalle\php7cc\Token\TokenCollection;

interface VisitorInterface extends NodeVisitor
{
    /**
     * @param ContextInterface $context
     */
    public function initializeContext(ContextInterface $context);

    /**
     * @param TokenCollection $tokenCollection
     */
    public function setTokenCollection(TokenCollection $tokenCollection);

    /**
     * @return int
     */
    public function getLevel();
}
