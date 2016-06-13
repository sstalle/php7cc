<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;
use Sstalle\php7cc\CompatibilityViolation\Message;

class ArrayOrObjectValueAssignmentByReferenceVisitor extends AbstractVisitor
{
    const LEVEL = Message::LEVEL_WARNING;

    /**
     * {@inheritdoc}
     */
    public function enterNode(Node $node)
    {
        if (!$node instanceof Node\Expr\AssignRef) {
            return;
        }

        $this->checkArrayValueByReferenceCreation($node) || $this->checkObjectPropertyByReferenceCreation($node);
    }

    /**
     * @param Node\Expr\AssignRef $node
     *
     * @return bool
     */
    protected function checkArrayValueByReferenceCreation(Node\Expr\AssignRef $node)
    {
        if ($node->var instanceof Node\Expr\ArrayDimFetch && $node->var->dim
            && $node->expr instanceof Node\Expr\ArrayDimFetch && $node->expr->dim
        ) {
            $this->addContextMessage(
                'Possible array element creation during by-reference assignment',
                $node
            );

            return true;
        }

        return false;
    }

    /**
     * @param Node\Expr\AssignRef $node
     *
     * @return bool
     */
    protected function checkObjectPropertyByReferenceCreation(Node\Expr\AssignRef $node)
    {
        if ($node->var instanceof Node\Expr\PropertyFetch && $node->expr instanceof Node\Expr\PropertyFetch) {
            $this->addContextMessage(
                'Possible object property creation during by-reference assignment',
                $node
            );

            return true;
        }

        return false;
    }
}
