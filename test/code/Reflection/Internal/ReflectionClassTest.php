<?php

namespace code\Reflection\Internal;

use Sstalle\php7cc\Reflection\Internal\ReflectionClass;

class ReflectionClassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ReflectionClass
     */
    private $reflectionClass;

    /**
     * @var \ReflectionMethod
     */
    private $internalReflectionMethod;

    /**
     * @var \ReflectionClass
     */
    private $internalReflectionClass;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->internalReflectionMethod = $this->buildReflectionMethod();
        $this->internalReflectionClass = $this->buildInternalReflectionClass($this->internalReflectionMethod);
        $this->reflectionClass = new ReflectionClass($this->internalReflectionClass);
    }

    public function testGetsNameFromInternalReflection()
    {
        $this->assertSame($this->internalReflectionClass->getName(), $this->reflectionClass->getName());
    }

    public function testGetsMethodFromInternalReflection()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('test');
        $this->assertSame($this->internalReflectionMethod->getName(), $reflectionMethod->getName());
    }

    public function testGetsMethodsFromInternalReflection()
    {
        $reflectionMethods = $this->reflectionClass->getMethods();
        $this->assertCount(1, $reflectionMethods);
        $reflectionMethod = $reflectionMethods[0];
        $this->assertSame($this->internalReflectionMethod->getName(), $reflectionMethod->getName());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\ReflectionMethod
     */
    private function buildReflectionMethod()
    {
        $internalReflectionMethod = $this->getMock('\ReflectionMethod', array('getName'), array(), '', false);
        $internalReflectionMethod->method('getName')
            ->willReturn('test');

        return $internalReflectionMethod;
    }

    /**
     * @param \ReflectionMethod $reflectionMethod
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\ReflectionClass
     */
    private function buildInternalReflectionClass(\ReflectionMethod $reflectionMethod)
    {
        $internalReflectionClass = $this->getMock(
            '\ReflectionClass',
            array('getName', 'getMethod', 'getMethods'),
            array(),
            '',
            false
        );

        $internalReflectionClass->method('getName')
            ->willReturn('Test');
        $internalReflectionClass->method('getMethod')
            ->willReturn($reflectionMethod);
        $internalReflectionClass->method('getMethods')
            ->willReturn(array($reflectionMethod));

        return $internalReflectionClass;
    }
}
