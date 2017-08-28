<?php

namespace Sstalle\php7cc\Reflection;

interface ReflectionClassInterface extends ReflectionInterface
{
    /**
     * @return ReflectionMethodInterface[]
     */
    public function getMethods();

    /**
     * @param string $methodName
     *
     * @return ReflectionMethodInterface|null
     */
    public function getMethod($methodName);
}
