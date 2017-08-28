<?php

namespace Sstalle\php7cc\Reflection\Internal\Reflector;

use Sstalle\php7cc\Reflection\Internal\ReflectionFunction;
use Sstalle\php7cc\Reflection\Reflector\FunctionReflectorInterface;

class FunctionReflector implements FunctionReflectorInterface
{
    /**
     * {@inheritdoc}
     */
    public function reflect($identifer)
    {
        return new ReflectionFunction($this->getInternalReflection($identifer));
    }

    /**
     * {@inheritdoc}
     */
    public function supports($identifier)
    {
        if (!function_exists($identifier)) {
            return false;
        }

        $internalReflection = $this->getInternalReflection($identifier);

        return !$internalReflection->isUserDefined();
    }

    /**
     * @param string $identifier
     *
     * @return \ReflectionFunction
     */
    private function getInternalReflection($identifier)
    {
        return new \ReflectionFunction($identifier);
    }
}
