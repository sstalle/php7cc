<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;

class HTTPRawPostDataVisitor extends AbstractVisitor
{
    const HTTP_RAW_POST_DATA_VARIABLE_NAME = 'HTTP_RAW_POST_DATA';

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Expr\Variable && $node->name === static::HTTP_RAW_POST_DATA_VARIABLE_NAME) {
            $this->addContextMessage(
                sprintf(
                    'Removed "%s" variable used',
                    static::HTTP_RAW_POST_DATA_VARIABLE_NAME
                ),
                $node
            );
        }
    }
}
