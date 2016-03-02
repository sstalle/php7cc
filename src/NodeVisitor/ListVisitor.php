<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;

class ListVisitor extends AbstractVisitor
{
    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Expr\List_) {
            $hasNonNullVar = false;
            foreach ($node->vars as $var) {
                if ($var !== null) {
                    $hasNonNullVar = true;
                    break;
                }
            }

            if (!$hasNonNullVar) {
                $this->addContextError(
                    'Empty list assignment',
                    $node
                );
            }
        }

        if ($node instanceof Node\Expr\Assign && $node->var instanceof Node\Expr\List_
            && ($node->expr instanceof Node\Scalar\String_ || $node->expr instanceof Node\Expr\Cast\String_)
        ) {
            $this->addContextError(
                'list unpacking string',
                $node
            );
        }
    }
}
