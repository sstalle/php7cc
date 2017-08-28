<?php

namespace Sstalle\php7cc\Reflection\Internal\Reflector;

use Sstalle\php7cc\Reflection\Internal\ReflectionClass;
use Sstalle\php7cc\Reflection\Reflector\ClassReflectorInterface;

class ClassReflector implements ClassReflectorInterface
{
    /**
     * {@inheritdoc}
     */
    public function reflect($identifer)
    {
        return new ReflectionClass($this->getInternalReflection($identifer));
    }

    /**
     * {@inheritdoc}
     */
    public function supports($identifier)
    {
        if (!class_exists($identifier)) {
            return false;
        }

        $internalReflection = $this->getInternalReflection($identifier);

        return !$internalReflection->isUserDefined();
    }

    /**
     * @param string $identifier
     *
     * @return \ReflectionClass
     */
    private function getInternalReflection($identifier)
    {
        return new \ReflectionClass($identifier);
    }
}
