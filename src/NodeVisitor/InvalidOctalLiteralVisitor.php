<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;

class InvalidOctalLiteralVisitor extends AbstractVisitor
{
    public function enterNode(Node $node)
    {
        if (!$node instanceof Node\Scalar\LNumber) {
            return;
        }

        $originalNumberValue = $node->getAttribute('originalValue', '');

        if (preg_match('/^0[0-7]*[89]+/', $originalNumberValue)) {
            $this->addContextError(
                sprintf('Invalid octal literal %s', $originalNumberValue),
                $node
            );
        }
    }
}
