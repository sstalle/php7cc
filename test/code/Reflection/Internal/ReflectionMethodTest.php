<?php

namespace code\Reflection\Internal;

use Sstalle\php7cc\Reflection\Internal\ReflectionMethod;

class ReflectionMethodTest extends AbstractReflectionFunctionAbstractTest
{
    /**
     * {@inheritdoc}
     */
    protected function buildReflection(\ReflectionFunctionAbstract $internalReflectionFunction)
    {
        return new ReflectionMethod($internalReflectionFunction);
    }
}
