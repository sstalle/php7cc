<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;
use Sstalle\php7cc\CompatibilityViolation\Message;

class ContinueBreakOutsideLoopVisitor extends AbstractNestedLoopVisitor
{
    const LEVEL = Message::LEVEL_ERROR;

    /**
     * {@inheritdoc}
     */
    public function enterNode(Node $node)
    {
        parent::enterNode($node);

        if (($node instanceof Node\Stmt\Break_ || $node instanceof Node\Stmt\Continue_)
            && $this->getCurrentLoopStack()->isEmpty()
        ) {
            $messageText = sprintf(
                '%s not in the loop or switch context',
                $node instanceof Node\Stmt\Break_ ? 'break' : 'continue'
            );
            $this->addContextMessage($messageText, $node);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function isTargetLoopNode(Node $node)
    {
        return $node instanceof Node\Stmt\While_ || $node instanceof Node\Stmt\Do_
            || $node instanceof Node\Stmt\Foreach_ || $node instanceof Node\Stmt\For_
            || $node instanceof Node\Stmt\Switch_;
    }
}
