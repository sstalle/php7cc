<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;

class YieldInExpressionContextVisitor extends AbstractVisitor
{
    /**
     * @var \SplStack
     */
    protected $expressionStack;

    public function beforeTraverse(array $nodes)
    {
        $this->expressionStack = new \SplStack();
    }

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
                $this->addContextWarning(
                    '"yield" usage in expression context',
                    $this->expressionStack->top()
                );
            }
        } elseif ($node instanceof Node\Expr) {
            $this->expressionStack->push($node);
        }
    }

    public function leaveNode(Node $node)
    {
        if (!$this->expressionStack->isEmpty() && $node === $this->expressionStack->top()) {
            $this->expressionStack->pop();
        }
    }
}
