<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;
use Sstalle\php7cc\CompatibilityViolation\Message;

class MultipleSwitchDefaultsVisitor extends AbstractVisitor
{
    const LEVEL = Message::LEVEL_ERROR;

    /**
     * {@inheritdoc}
     */
    public function enterNode(Node $node)
    {
        if (!$node instanceof Node\Stmt\Switch_) {
            return;
        }

        $defaultCaseCount = 0;
        foreach ($node->cases as $case) {
            if ($case->cond === null) {
                ++$defaultCaseCount;
            }
        }

        if ($defaultCaseCount > 1) {
            $this->addContextMessage(
                'Multiple default cases defined for the switch statement',
                $node
            );
        }
    }
}
