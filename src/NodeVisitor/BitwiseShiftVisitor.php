<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;

class BitwiseShiftVisitor extends AbstractVisitor
{

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
        }
    }

}