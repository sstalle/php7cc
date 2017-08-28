<?php

namespace Sstalle\php7cc\NodeAnalyzer\Reflection\FunctionLike;

use PhpParser\Node\Expr;
use Sstalle\php7cc\Reflection\ReflectionFunctionAbstractInterface;

interface CalleeReflectorInterface
{
    /**
     * @param Expr\StaticCall|Expr\FuncCall|Expr\MethodCall $node
     *
     * @return ReflectionFunctionAbstractInterface
     */
    public function reflect($node);

    /**
     * @param Expr\StaticCall|Expr\FuncCall|Expr\MethodCall $node
     *
     * @return bool
     */
    public function supports($node);
}
