<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;

class IndirectVariableOrMethodAccessVisitor extends AbstractVisitor
{
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

        $this->addContextWarning(
            'Indirect variable, property or method access',
            $node
        );
    }
}
