<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;

class GlobalVariableVariableVisitor extends AbstractVisitor
{
    public function enterNode(Node $node)
    {
        if (!$node instanceof Node\Stmt\Global_) {
            return;
        }

        foreach ($node->vars as $globalVariable) {
            if (!(
                $globalVariable->name instanceof Node\Expr\PropertyFetch
                || $globalVariable->name instanceof Node\Expr\StaticPropertyFetch
                || $globalVariable->name instanceof Node\Expr\ArrayDimFetch
                )
            ) {
                continue;
            }

            $startTokenPosition = $globalVariable->getAttribute('startTokenPos') + 1;
            $endTokenPosition = $globalVariable->getAttribute('endTokenPos');
            if ($this->tokenCollection->isTokenEqualToOrPrecededBy($startTokenPosition, '{')
                && $this->tokenCollection->isTokenEqualToOrFollowedBy($endTokenPosition, '}')
            ) {
                continue;
            }

            $this->addContextMessage(
                'Complex variable without curly braces in global keyword',
                $node
            );
        }
    }
}
