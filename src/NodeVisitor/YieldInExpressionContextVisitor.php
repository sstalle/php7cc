<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;

class YieldInExpressionContextVisitor extends AbstractVisitor
{
    /**
     * @var \SplStack
     */
    protected $expressionStack;

    protected $ignoredExpressionClasses = array(
        'PhpParser\\Node\\Expr\\Assign',
        'PhpParser\\Node\\Expr\\AssignRef',
        'PhpParser\\Node\\Expr\\AssignOp',
    );

    public function beforeTraverse(array $nodes)
    {
        $this->expressionStack = new \SplStack();
    }


    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Expr\Yield_) {
            if (!$this->expressionStack->isEmpty()) {
                $this->addContextMessage(
                    '"yield" usage in expression context',
                    $this->expressionStack->top()
                );
            }
        } elseif ($node instanceof Node\Expr) {
            foreach ($this->ignoredExpressionClasses as $ignoredClass) {
                if (is_a($node, $ignoredClass)) {
                    return;
                }
            }

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