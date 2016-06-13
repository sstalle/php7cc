<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;
use Sstalle\php7cc\CompatibilityViolation\Message;

class InvalidOctalLiteralVisitor extends AbstractVisitor
{
    const LEVEL = Message::LEVEL_ERROR;

    /**
     * {@inheritdoc}
     */
    public function enterNode(Node $node)
    {
        if (!$node instanceof Node\Scalar\LNumber) {
            return;
        }

        $originalNumberValue = $node->getAttribute('originalValue', '');

        if (preg_match('/^0[0-7]*[89]+/', $originalNumberValue)) {
            $this->addContextMessage(
                sprintf('Invalid octal literal %s', $originalNumberValue),
                $node
            );
        }
    }
}
