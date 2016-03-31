<?php

namespace Sstalle\php7cc\NodeAnalyzer;

use PhpParser\Node;

class FunctionAnalyzer
{
    /**
     * @param Node            $node
     * @param string|string[] $checkedFunctionName If an array as passed, function names should be keys
     *
     * @return bool
     */
    public function isFunctionCallByStaticName(Node $node, $checkedFunctionName)
    {
        $isFunctionCallByStaticName = $node instanceof Node\Expr\FuncCall && $node->name instanceof Node\Name;
        if (!$isFunctionCallByStaticName) {
            return $isFunctionCallByStaticName;
        }

        $calledFunctionName = strtolower($node->name->toString());

        return is_array($checkedFunctionName)
            ? isset($checkedFunctionName[$calledFunctionName])
            : $calledFunctionName === $checkedFunctionName;
    }
}
