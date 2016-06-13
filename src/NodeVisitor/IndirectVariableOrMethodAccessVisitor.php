<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;
use Sstalle\php7cc\CompatibilityViolation\Message;

class IndirectVariableOrMethodAccessVisitor extends AbstractVisitor
{
    const LEVEL = Message::LEVEL_WARNING;

    /**
     * {@inheritdoc}
     */
    public function enterNode(Node $node)
    {
        if (!($node instanceof Node\Expr\PropertyFetch
                || $node instanceof Node\Expr\MethodCall
                || $node instanceof Node\Expr\StaticCall
                || $node instanceof Node\Expr\Variable
            ) || !$node->name instanceof Node\Expr\ArrayDimFetch
        ) {
            return;
        }

        $nodeName = $node->name;
        if ($this->tokenCollection->isTokenEqualToOrPrecededBy($nodeName->getAttribute('startTokenPos') - 1, '{')
            && $this->tokenCollection->isTokenEqualToOrFollowedBy($nodeName->getAttribute('endTokenPos') + 1, '}')
        ) {
            return;
        }

        $this->addContextMessage(
            'Indirect variable, property or method access',
            $node
        );
    }
}
