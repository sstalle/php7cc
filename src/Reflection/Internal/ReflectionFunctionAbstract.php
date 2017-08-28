<?php

namespace Sstalle\php7cc\Reflection\Internal;

use Sstalle\php7cc\Reflection\ReflectionFunctionAbstractInterface;

class ReflectionFunctionAbstract implements ReflectionFunctionAbstractInterface
{
    /**
     * @var \ReflectionFunctionAbstract
     */
    private $internalReflectionFunction;

    /**
     * @param \ReflectionFunctionAbstract $reflection
     */
    public function __construct(\ReflectionFunctionAbstract $reflection)
    {
        $this->internalReflectionFunction = $reflection;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->internalReflectionFunction->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        $parameters = array();
        foreach ($this->internalReflectionFunction->getParameters() as $parameter) {
            $parameters[] = new ReflectionParameter($parameter);
        }

        return $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameter($parameterIndex)
    {
        $parameters = $this->getParameters();

        return isset($parameters[$parameterIndex]) ? $parameters[$parameterIndex] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getByReferenceParameterPositions()
    {
        return array_map(function (ReflectionParameter $parameter) {
            return $parameter->getPosition();
        }, $this->getByReferenceParameters());
    }

    /**
     * @return ReflectionParameter[]
     */
    private function getByReferenceParameters()
    {
        return array_filter($this->getParameters(), function (ReflectionParameter $parameter) {
            return $parameter->isPassedByReference();
        });
    }
}
