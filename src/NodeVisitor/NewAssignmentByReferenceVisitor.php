<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;

class NewAssignmentByReferenceVisitor extends AbstractVisitor
{

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