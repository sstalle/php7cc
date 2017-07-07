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
        if ($node instanceof Node\Expr\Yield_ && !$this->expressionStack->isEmpty()) {
            $startTokenPosition = $node->getAttribute('startTokenPos');
            $endTokenPosition = $node->getAttribute('endTokenPos');

            if (!$this->tokenCollection->isTokenPrecededBy($startTokenPosition, '(')
                || !$this->tokenCollection->isTokenFollowedBy($endTokenPosition, ')')
            ) {
                $this->addContextMessage(
                    '"yield" usage in expression context',
                    $this->expressionStack->top()
                );
            }
        } elseif ($node instanceof Node\Expr && !$this->isIgnoredExpressionType($node)) {
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

    /**
     * @param Node\Expr $expr
     *
     * @return bool
     */
    private function isIgnoredExpressionType(Node\Expr $expr)
    {
        /**
         * We don't care about assignments, because:
         * 1. If the assigned expression ($expr->expr) is a yield expression, then:
         *  a. It had been added before moving to 7.x and is parenthesized (assigning
         *     yield expressions without parenthesis causes a syntax error in 5.x).
         *     And parenthesized yields have the same meaning in both 5.x and 7.x
         *  b. It had been added after moving to 7.x and should be left alone
         * 2. If the assigned expression is not a yield expression, then:
         *  a. It has a nested yield expression which will get caught by the enterNode method
         *  b. It has no nested yield expression and is not relevant to this visitor.
         */
        $isAssignment = $expr instanceof Node\Expr\Assign || $expr instanceof Node\Expr\AssignOp;
        $isClosureDeclaration = $expr instanceof Node\Expr\Closure;
        $isAnonymousClassInstantiation = $expr instanceof Node\Expr\New_
            && $expr->class instanceof Node\Stmt\Class_
            && !$expr->class->name;

        return $isAssignment
            || $isClosureDeclaration
            || $isAnonymousClassInstantiation
        ;
    }
}
