<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;
use Sstalle\php7cc\CompatibilityViolation\Message;

class YieldInExpressionContextVisitor extends AbstractVisitor
{
    const LEVEL = Message::LEVEL_WARNING;

    /**
     * @var \SplStack
     */
    protected $expressionStack;

    /**
     * {@inheritdoc}
     */
    public function beforeTraverse(array $nodes)
    {
        $this->expressionStack = new \SplStack();
    }

    /**
     * {@inheritdoc}
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Expr\Yield_) {
            $startTokenPosition = $node->getAttribute('startTokenPos');
            $endTokenPosition = $node->getAttribute('endTokenPos');

            if (!(
                    $this->tokenCollection->isTokenPrecededBy($startTokenPosition, '(')
                    && $this->tokenCollection->isTokenFollowedBy($endTokenPosition, ')')
                )
                && !$this->expressionStack->isEmpty()
            ) {
                $this->addContextMessage(
                    '"yield" usage in expression context',
                    $this->expressionStack->top()
                );
            }
        } elseif ($node instanceof Node\Expr) {
            $this->expressionStack->push($node);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function leaveNode(Node $node)
    {
        if (!$this->expressionStack->isEmpty() && $node === $this->expressionStack->top()) {
            $this->expressionStack->pop();
        }
    }
}
