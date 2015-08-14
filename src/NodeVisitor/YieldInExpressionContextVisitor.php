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
                    $this->isImmediateSiblingOfToken($startTokenPosition, true, '(')
                    && $this->isImmediateSiblingOfToken($endTokenPosition, false, ')')
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

    public function leaveNode(Node $node)
    {
        if (!$this->expressionStack->isEmpty() && $node === $this->expressionStack->top()) {
            $this->expressionStack->pop();
        }
    }

    /**
     * Returns true if $sourceTokenPosition is separated from $targetToken by whitespace tokens only,
     * otherwise false
     *
     * @param string $sourceTokenPosition Position in the token array to start search from
     * @param bool $before true - search before source token, false - after
     * @param string $targetToken Token value to search for
     * @return bool
     */
    protected function isImmediateSiblingOfToken($sourceTokenPosition, $before, $targetToken)
    {
        while (isset($this->tokens[$before ? --$sourceTokenPosition : ++$sourceTokenPosition])) {
            $currentToken = $this->tokens[$sourceTokenPosition];
            if (is_array($currentToken) && preg_match('/\s+/', $currentToken[1]) === 1) {
                continue;
            }

            return $currentToken === $targetToken;
        }

        return false;
    }

}