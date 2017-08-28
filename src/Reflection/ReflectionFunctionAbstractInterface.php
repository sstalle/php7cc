<?php

namespace Sstalle\php7cc\Reflection;

interface ReflectionFunctionAbstractInterface extends ReflectionInterface
{
    /**
     * @return ReflectionParameterInterface[]
     */
    public function getParameters();

    /**
     * @param int $parameterIndex
     *
     * @return ReflectionParameterInterface|null
     */
    public function getParameter($parameterIndex);

    /**
     * @return int[]
     */
    public function getByReferenceParameterPositions();
}
