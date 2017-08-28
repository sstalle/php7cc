<?php

namespace Sstalle\php7cc\Reflection\Internal;

use Sstalle\php7cc\Reflection\ReflectionParameterInterface;

class ReflectionParameter implements ReflectionParameterInterface
{
    /**
     * @var \ReflectionParameter
     */
    private $internalReflection;

    /**
     * @param \ReflectionParameter $internalReflection
     */
    public function __construct(\ReflectionParameter $internalReflection)
    {
        $this->internalReflection = $internalReflection;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->internalReflection->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function isPassedByReference()
    {
        return $this->internalReflection->isPassedByReference();
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return $this->internalReflection->getPosition();
    }
}
