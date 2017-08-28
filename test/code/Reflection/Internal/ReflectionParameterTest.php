<?php

namespace code\Reflection\Internal;

use Sstalle\php7cc\Reflection\Internal\ReflectionParameter;

class ReflectionParameterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider internalReflectionParameterProvider
     *
     * @param string $name
     */
    public function testGetsNameFromInternalReflection($name)
    {
        $reflection = new ReflectionParameter($this->buildInternalReflectionParameter($name, 0, false));
        $this->assertSame($name, $reflection->getName());
    }

    /**
     * @dataProvider internalReflectionParameterProvider
     *
     * @param string $name
     * @param int    $position
     * @param bool   $isPassedByReference
     */
    public function testGetsPassedByReferenceFromInternalReflection($name, $position, $isPassedByReference)
    {
        $reflection = new ReflectionParameter($this->buildInternalReflectionParameter($name, $position, $isPassedByReference));
        $this->assertSame($isPassedByReference, $reflection->isPassedByReference());
    }

    /**
     * @dataProvider internalReflectionParameterProvider
     *
     * @param string $name
     * @param int    $position
     * @param bool   $isPassedByReference
     */
    public function testGetsPositionFromInternalReflection($name, $position, $isPassedByReference)
    {
        $reflection = new ReflectionParameter($this->buildInternalReflectionParameter($name, $position, $isPassedByReference));
        $this->assertSame($isPassedByReference, $reflection->isPassedByReference());
    }

    /**
     * @return array
     */
    public function internalReflectionParameterProvider()
    {
        return array(
            array(
                'testByRef',
                0,
                true,
            ),
            array(
                'testByVal',
                0,
                true,
            ),
            array(
                'testNonZeroPosition',
                3,
                false,
            ),
        );
    }

    /**
     * @param string $name
     * @param int    $position
     * @param bool   $isPassedByReference
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\ReflectionParameter
     */
    private function buildInternalReflectionParameter($name, $position, $isPassedByReference)
    {
        $internalReflectionParameter = $this->getMock(
            '\ReflectionParameter',
            array('getName', 'isPassedByReference', 'getPosition'),
            array(),
            '',
            false
        );

        $internalReflectionParameter->method('getName')
            ->willReturn($name);
        $internalReflectionParameter->method('getPosition')
            ->willReturn($position);
        $internalReflectionParameter->method('isPassedByReference')
            ->willReturn($isPassedByReference);

        return $internalReflectionParameter;
    }
}
