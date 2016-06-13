<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;
use Sstalle\php7cc\CompatibilityViolation\Message;

class DivisionModuloByZeroVisitor extends AbstractVisitor
{
    const LEVEL = Message::LEVEL_ERROR;

    /**
     * {@inheritdoc}
     */
    public function enterNode(Node $node)
    {
        $isDivision = $node instanceof Node\Expr\BinaryOp\Div || $node instanceof Node\Expr\AssignOp\Div;
        $isModulo = $node instanceof Node\Expr\BinaryOp\Mod || $node instanceof Node\Expr\AssignOp\Mod;

        if (!($isDivision || $isModulo)) {
            return;
        }

        $divisor = $node instanceof Node\Expr\BinaryOp ? $node->right : $node->expr;
        if ($divisor instanceof Node\Scalar\LNumber && $divisor->value == 0) {
            $this->addContextMessage(
                sprintf('%s by zero', $isDivision ? 'Division' : 'Modulo'),
                $node
            );
        }
    }
}
