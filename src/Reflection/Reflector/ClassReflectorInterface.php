<?php

namespace Sstalle\php7cc\Reflection\Reflector;

use Sstalle\php7cc\Reflection\ReflectionClassInterface;

interface ClassReflectorInterface extends ReflectorInterface
{
    /**
     * @param string $identifer
     *
     * @return ReflectionClassInterface
     */
    public function reflect($identifer);
}
