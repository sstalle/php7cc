<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;
use Sstalle\php7cc\CompatibilityViolation\Message;

class BitwiseShiftVisitor extends AbstractVisitor
{
    const LEVEL = Message::LEVEL_ERROR;
    const MIN_INT_SIZE = 32;

    /**
     * @var int
     */
    protected $intSize;

    /**
     * @param $intSize
     */
    public function __construct($intSize = self::MIN_INT_SIZE)
    {
        if ($intSize <= 0) {
            throw new \InvalidArgumentException(sprintf('Integer size must be greater than 0, %d given', $intSize));
        }

        $this->intSize = $intSize;
    }

    /**
     * {@inheritdoc}
     */
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
                'Bitwise shift by a negative number',
                $node
            );
        } elseif ($rightOperand instanceof Node\Scalar\LNumber && $rightOperand->value >= $this->intSize) {
            $this->addContextMessage(
                sprintf('Bitwise shift by %d bits', $rightOperand->value),
                $node
            );
        }
    }
}
