<?php

namespace Sstalle\php7cc\Reflection\Reflector;

use Sstalle\php7cc\Reflection\ReflectionFunctionInterface;

interface FunctionReflectorInterface extends ReflectorInterface
{
    /**
     * @param string $identifer
     *
     * @return ReflectionFunctionInterface
     */
    public function reflect($identifer);
}
