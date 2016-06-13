<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;
use Sstalle\php7cc\CompatibilityViolation\Message;

class HexadecimalNumberStringVisitor extends AbstractVisitor
{
    const LEVEL = Message::LEVEL_WARNING;

    /**
     * {@inheritdoc}
     */
    public function enterNode(Node $node)
    {
        if (!$node instanceof Node\Scalar\String_) {
            return;
        }

        if (preg_match('/^0x[a-fA-F0-9]+$/', $node->value)) {
            $this->addContextMessage(
                'String containing number in hexadecimal notation',
                $node
            );
        }
    }
}
