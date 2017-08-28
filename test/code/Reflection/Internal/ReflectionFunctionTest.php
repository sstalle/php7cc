<?php

namespace code\Reflection\Internal;

use Sstalle\php7cc\Reflection\Internal\ReflectionFunction;

class ReflectionFunctionTest extends AbstractReflectionFunctionAbstractTest
{
    /**
     * {@inheritdoc}
     */
    protected function buildReflection(\ReflectionFunctionAbstract $internalReflectionFunction)
    {
        return new ReflectionFunction($internalReflectionFunction);
    }
}
