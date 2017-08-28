<?php

namespace Sstalle\php7cc\Reflection\Reflector;

use Sstalle\php7cc\Reflection\ReflectionInterface;

interface ReflectorInterface
{
    /**
     * @param string $identifer
     *
     * @return ReflectionInterface
     */
    public function reflect($identifer);

    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function supports($identifier);
}
