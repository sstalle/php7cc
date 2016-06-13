<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;
use Sstalle\php7cc\CompatibilityViolation\Message;

class NewAssignmentByReferenceVisitor extends AbstractVisitor
{
    const LEVEL = Message::LEVEL_ERROR;

    /**
     * {@inheritdoc}
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Expr\AssignRef && $node->expr instanceof Node\Expr\New_) {
            $this->addContextMessage(
                'Result of new is assigned by reference',
                $node
            );
        }
    }
}
