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
            )) {
                continue;
            }
            ;
            $nextToStartToken = $this->tokens[$globalVariable->getAttribute('startTokenPos') + 1];
            $endToken = $this->tokens[$globalVariable->getAttribute('endTokenPos')];
            if ($nextToStartToken === '{' && $endToken === '}') {
                continue;
            }

            $this->addContextMessage(
                'Complex variable without curly braces in global keyword',
                $node
            );
        }
    }

}