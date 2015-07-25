<?php

namespace Sstalle\php7cc\Helper;

use PhpParser\Node;

class NodeHelper
{

    /**
     * @param Node $node
     * @return bool
     */
    public static function isFunctionCallByStaticName(Node $node) {
        return $node instanceof Node\Expr\FuncCall && $node->name instanceof Node\Name;
    }

}