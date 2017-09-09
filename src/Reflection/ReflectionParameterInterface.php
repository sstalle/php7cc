<?php

namespace Sstalle\php7cc\Reflection;

interface ReflectionParameterInterface extends ReflectionInterface
{
    /**
     * @return bool
     */
    public function isPassedByReference();

    /**
     * @return int
     */
    public function getPosition();
}
