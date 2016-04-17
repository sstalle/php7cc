<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;
use Sstalle\php7cc\CompatibilityViolation\Message;

class GlobalNewFunctionVisitor extends AbstractNewFunctionVisitor
{
    const LEVEL = Message::LEVEL_ERROR;

    /**
     * {@inheritdoc}
     */
    protected function accepts(Node\Stmt\Function_ $node)
    {
        return isset($node->namespacedName) && count($node->namespacedName->parts) == 1;
    }

    /**
     * {@inheritdoc}
     */
    protected function getMessageText($functionName)
    {
        return sprintf('Cannot redeclare global function "%s"', $functionName);
    }
}
