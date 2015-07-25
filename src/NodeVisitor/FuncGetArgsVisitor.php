<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;
use Sstalle\php7cc\Helper\NodeHelper;

class FuncGetArgsVisitor extends AbstractVisitor
{

    public function enterNode(Node $node)
    {
        if (!NodeHelper::isFunctionCallByStaticName($node)) {
            return;
        }

        /** @var Node\Expr\FuncCall $node */
        $functionName = $node->name->toString();
        if ($functionName == 'func_get_arg' || $functionName == 'func_get_args') {
            $this->addContextMessage(
                sprintf('Function argument(s) returned by "%s" might have been modified', $functionName),
                $node
            );
        }
    }
}