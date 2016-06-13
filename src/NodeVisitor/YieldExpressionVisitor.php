<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;
use Sstalle\php7cc\CompatibilityViolation\Message;

class YieldExpressionVisitor extends AbstractVisitor
{
    const LEVEL = Message::LEVEL_WARNING;

    /**
     * @var string[]
     */
    protected $lowerPrecedenceExpressionClasses = array(
        'PhpParser\\Node\\Expr\\BinaryOp\\LogicalAnd',
        'PhpParser\\Node\\Expr\\BinaryOp\\LogicalOr',
        'PhpParser\\Node\\Expr\\BinaryOp\\LogicalXor',
    );

    public function __construct()
    {
        $this->lowerPrecedenceExpressionClasses = array_flip($this->lowerPrecedenceExpressionClasses);
    }

    /**
     * {@inheritdoc}
     */
    public function enterNode(Node $node)
    {
        if (!($node instanceof Node\Expr\Yield_ && $node->value && $node->value instanceof Node\Expr)) {
            return;
        }

        $valueClass = get_class($node->value);
        if (isset($this->lowerPrecedenceExpressionClasses[$valueClass])) {
            $this->addContextMessage(
                'Yielding expression with precedence lower than "yield"',
                $node
            );
        }
    }
}
