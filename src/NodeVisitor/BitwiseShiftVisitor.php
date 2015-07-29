<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;

class BitwiseShiftVisitor extends AbstractVisitor
{

    const MIN_INT_SIZE = 32;

    public function enterNode(Node $node)
    {
        $isLeftShift = $node instanceof Node\Expr\BinaryOp\ShiftLeft;
        $isRightShift = $node instanceof Node\Expr\BinaryOp\ShiftRight;
        if (!$isLeftShift && !$isRightShift) {
            return;
        }

        $rightOperand = $node->right;
        if ($rightOperand instanceof Node\Expr\UnaryMinus && $rightOperand->expr instanceof Node\Scalar\LNumber
            && $rightOperand->expr->value > 0
        ) {
            $this->addContextMessage(
                'Bitwise shift by negative number',
                $node
            );
        } elseif ($rightOperand instanceof Node\Scalar\LNumber && $rightOperand->value >= static::MIN_INT_SIZE) {
            $this->addContextMessage(
                sprintf('Bitwise shift by %d bits', $rightOperand->value),
                $node
            );
        }
    }

}