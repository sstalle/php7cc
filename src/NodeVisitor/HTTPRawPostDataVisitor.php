<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;

class HTTPRawPostDataVisitor extends AbstractVisitor
{
    const HTTP_RAW_POST_DATA_VARIABLE_NAME = 'HTTP_RAW_POST_DATA';

    public function enterNode(Node $node)
    {
        $isVariableAccessedByName = $node instanceof Node\Expr\Variable
            && $node->name === static::HTTP_RAW_POST_DATA_VARIABLE_NAME;
        $isVariableAccessedThroughGlobals = $node instanceof Node\Expr\ArrayDimFetch
            && $node->var instanceof Node\Expr\Variable
            && $node->var->name == 'GLOBALS'
            && $node->dim instanceof Node\Scalar\String_
            && $node->dim->value === static::HTTP_RAW_POST_DATA_VARIABLE_NAME;

        if ($isVariableAccessedByName || $isVariableAccessedThroughGlobals) {
            $this->addContextError(
                sprintf(
                    'Removed "%s" variable used',
                    static::HTTP_RAW_POST_DATA_VARIABLE_NAME
                ),
                $node
            );
        }
    }
}
