<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;

class HexadecimalNumberStringVisitor extends AbstractVisitor
{

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