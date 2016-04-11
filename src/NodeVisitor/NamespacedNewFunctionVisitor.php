<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;
use Sstalle\php7cc\CompatibilityViolation\Message;

class NamespacedNewFunctionVisitor extends AbstractNewFunctionVisitor
{
    const LEVEL = Message::LEVEL_WARNING;

    /**
     * {@inheritdoc}
     */
    protected function accepts(Node\Stmt\Function_ $node)
    {
        return isset($node->namespacedName) && count($node->namespacedName->parts) > 1;
    }

    /**
     * {@inheritdoc}
     */
    protected function getMessageText($functionName)
    {
        return sprintf(
            'Your namespaced function "%s" could replace the new global function added in PHP 7',
            $functionName
        );
    }
}
