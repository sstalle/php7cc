<?php

namespace Sstalle\php7cc\Reflection\Internal;

use Sstalle\php7cc\Reflection\ReflectionClassInterface;

class ReflectionClass implements ReflectionClassInterface
{
    /**
     * @var \ReflectionClass
     */
    private $internalReflectionClass;

    /**
     * @param \ReflectionClass $internalReflectionClass
     */
    public function __construct(\ReflectionClass $internalReflectionClass)
    {
        $this->internalReflectionClass = $internalReflectionClass;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethods()
    {
        $methods = array();
        foreach ($this->internalReflectionClass->getMethods() as $method) {
            $methods[] = new ReflectionMethod($method);
        }

        return $methods;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod($methodName)
    {
        return new ReflectionMethod($this->internalReflectionClass->getMethod($methodName));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->internalReflectionClass->getName();
    }
}
