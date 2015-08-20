<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;

class IndirectVariableOrMethodAccessVisitor extends AbstractVisitor
{
    public function enterNode(Node $node)
    {
        $endCurlyBraceOffset = 0;
        $startCurlyBraceOffset = 2;

        if (($node instanceof Node\Expr\PropertyFetch
                || $node instanceof Node\Expr\MethodCall
                || $node instanceof Node\Expr\StaticCall
                || $node instanceof Node\Expr\Variable
            ) && $node->name instanceof Node\Expr\ArrayDimFetch
        ) {
            if ($node instanceof Node\Expr\Variable) {
                $startCurlyBraceOffset = 1;
            } elseif (!$node instanceof Node\Expr\PropertyFetch) {
                $endCurlyBraceOffset = -2;
            }
        } else {
            return;
        }

        $nextToStartToken = $this->tokens[$node->getAttribute('startTokenPos') + $startCurlyBraceOffset];
        $endToken = $this->tokens[$node->getAttribute('endTokenPos') + $endCurlyBraceOffset];
        if ($nextToStartToken === '{' && $endToken === '}') {
            return;
        }

        $this->addContextMessage(
            'Indirect variable, property or method access',
            $node
        );
    }
}
