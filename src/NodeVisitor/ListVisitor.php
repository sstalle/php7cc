<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;
use Sstalle\php7cc\CompatibilityViolation\Message;

class ListVisitor extends AbstractVisitor
{
    const LEVEL = Message::LEVEL_ERROR;

    /**
     * {@inheritdoc}
     */
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
                $this->addContextMessage(
                    'Empty list assignment',
                    $node
                );
            }
        }

        if ($node instanceof Node\Expr\Assign && $node->var instanceof Node\Expr\List_
            && ($node->expr instanceof Node\Scalar\String_ || $node->expr instanceof Node\Expr\Cast\String_)
        ) {
            $this->addContextMessage(
                'list unpacking string',
                $node
            );
        }
    }
}
