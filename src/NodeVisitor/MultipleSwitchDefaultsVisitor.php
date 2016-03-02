<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;

class MultipleSwitchDefaultsVisitor extends AbstractVisitor
{
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
            $this->addContextError(
                'Multiple default cases defined for the switch statement',
                $node
            );
        }
    }
}
