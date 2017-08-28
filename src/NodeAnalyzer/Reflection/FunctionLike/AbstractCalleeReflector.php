<?php

namespace Sstalle\php7cc\NodeAnalyzer\Reflection\FunctionLike;

use PhpParser\Node\Expr;
use Sstalle\php7cc\NodeAnalyzer\Reflection\Exception\UnsupportedNodeTypeException;
use Sstalle\php7cc\Reflection\ReflectionFunctionAbstractInterface;

abstract class AbstractCalleeReflector implements CalleeReflectorInterface
{
    /**
     * {@inheritdoc}
     */
    final public function reflect($node)
    {
        if (!$this->supports($node)) {
            throw new UnsupportedNodeTypeException(sprintf('Unsupported node type %s', get_class($node)));
        }

        return $this->doGetCalleeReflection($node);
    }

    /**
     * @param Expr\StaticCall|Expr\FuncCall|Expr\MethodCall $node
     *
     * @return ReflectionFunctionAbstractInterface
     */
    abstract protected function doGetCalleeReflection($node);
}
