<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;
use Sstalle\php7cc\Helper\NodeHelper;

class FuncGetArgsVisitor extends AbstractVisitor
{

    public function enterNode(Node $node)
    {
        if (!NodeHelper::isFunctionCallByStaticName($node, array_flip(array('func_get_arg', 'func_get_args')))) {
            return;
        }

        /** @var Node\Expr\FuncCall $node */
        $functionName = $node->name->toString();
        $this->addContextMessage(
            sprintf('Function argument(s) returned by "%s" might have been modified', $functionName),
            $node
        );
    }
}