<?php

namespace code\Reflection\Internal;

use Sstalle\php7cc\Reflection\Internal\ReflectionFunctionAbstract;

abstract class AbstractReflectionFunctionAbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param \ReflectionFunctionAbstract $internalReflectionFunction
     *
     * @return ReflectionFunctionAbstract
     */
    abstract protected function buildReflection(\ReflectionFunctionAbstract $internalReflectionFunction);

    /**
     * @dataProvider getsNamesFromInternalReflectionProvider
     *
     * @param string $name
     */
    public function testGetsNameFromInternalReflection($name)
    {
        $reflection = $this->buildReflection($this->buildInternalReflectionFunctionAbstract($name));

        $this->assertSame($name, $reflection->getName());
    }

    /**
     * @dataProvider byReferenceParametersProvider
     *
     * @param string                 $name
     * @param \ReflectionParameter[] $internalReflectionParameters
     */
    public function testGetsParametersFromInternalReflection($name, $internalReflectionParameters)
    {
        $reflection = $this->buildReflection($this->buildInternalReflectionFunctionAbstract($name, $internalReflectionParameters));

        $actualParameters = $reflection->getParameters();
        $this->assertSame(count($internalReflectionParameters), count($actualParameters));
        foreach ($internalReflectionParameters as $i => $parameter) {
            $this->assertTrue(isset($actualParameters[$i]));
            $actualParameter = $actualParameters[$i];
            $this->assertSame($parameter->getName(), $actualParameter->getName());
        }
    }

    /**
     * @dataProvider byReferenceParametersProvider
     *
     * @param string                 $name
     * @param \ReflectionParameter[] $internalReflectionParameters
     */
    public function testExtractsByReferenceParameterPositionsCorrectly($name, $internalReflectionParameters)
    {
        $reflection = $this->buildReflection($this->buildInternalReflectionFunctionAbstract($name, $internalReflectionParameters));
        $expectedByReferencePositions = array();
        foreach ($internalReflectionParameters as $position => $parameter) {
            if ($parameter->isPassedByReference()) {
                $expectedByReferencePositions[] = $position;
            }
        }

        $this->assertSame($expectedByReferencePositions, $reflection->getByReferenceParameterPositions());
    }

    /**
     * @dataProvider getParameterByIndexFromInternalReflectionTest
     *
     * @param string                 $name
     * @param \ReflectionParameter[] $internalReflectionParameters
     * @param int                    $targetParameterIndex
     * @param string|null            $expectedParameterName
     */
    public function testGetParameterByIndexFromInternalReflection(
        $name,
        $internalReflectionParameters,
        $targetParameterIndex,
        $expectedParameterName
    ) {
        $reflection = $this->buildReflection($this->buildInternalReflectionFunctionAbstract($name, $internalReflectionParameters));

        $reflectionParameter = $reflection->getParameter($targetParameterIndex);
        if ($expectedParameterName === null) {
            $this->assertNull($reflection->getParameter($targetParameterIndex));
        } else {
            $this->assertSame($expectedParameterName, $reflectionParameter->getName());
        }
    }

    /**
     * @return array
     */
    public function getsNamesFromInternalReflectionProvider()
    {
        return array(
            array('test'),
            array('foo'),
        );
    }

    /**
     * @return array
     */
    public function byReferenceParametersProvider()
    {
        return array(
            array(
                'testAllByValueParameters',
                $this->buildInternalReflectionParameters(array(
                    array('test1', false),
                    array('test2', false),
                )),
            ),
            array(
                'testFirstByReferenceParameter',
                $this->buildInternalReflectionParameters(array(
                    array('test1', true),
                    array('test2', false),
                )),
            ),
            array(
                'testAllByReferenceParameters',
                $this->buildInternalReflectionParameters(array(
                    array('test1', true),
                    array('test2', true),
                    array('test3', true),
                )),
            ),
            array(
                'testNoParameters',
                array(),
            ),
        );
    }

    /**
     * @return array
     */
    public function getParameterByIndexFromInternalReflectionTest()
    {
        return array(
            array(
                'test1Parameter',
                $this->buildInternalReflectionParameters(array(
                    array('test1', false),
                )),
                0,
                'test1',
            ),
            array(
                'test3Parameters',
                $this->buildInternalReflectionParameters(array(
                    array('test1', false),
                    array('test2', false),
                    array('test3', false),
                )),
                2,
                'test3',
            ),
            array(
                'testNoParameters',
                array(),
                0,
                null,
            ),
            array(
                'testParameterIndexOutOfBounds',
                $this->buildInternalReflectionParameters(array(
                    array('test1', false),
                    array('test2', false),
                    array('test3', false),
                )),
                5,
                null,
            ),
        );
    }

    /**
     * @param string                 $name
     * @param \ReflectionParameter[] $parameters
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\ReflectionFunctionAbstract
     */
    private function buildInternalReflectionFunctionAbstract($name, array $parameters = array())
    {
        $internalReflectionFunction = $this->getMock('\ReflectionFunctionAbstract', array(), array(), '', false);
        $internalReflectionFunction->method('getName')
            ->willReturn($name);

        $internalReflectionFunction->method('getParameters')
            ->willReturn($parameters);

        return $internalReflectionFunction;
    }

    /**
     * @param array $parametersData
     *
     * @return \ReflectionParameter[]
     */
    private function buildInternalReflectionParameters(array $parametersData)
    {
        $internalReflectionParameters = array();
        foreach ($parametersData as $position => $parameterData) {
            list($parameterName, $isPassedByReference) = $parameterData;
            $internalReflectionParameters[] = $this->buildInternalReflectionParameter($parameterName, $position, $isPassedByReference);
        }

        return $internalReflectionParameters;
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
            array('getName', 'getPosition', 'isPassedByReference'),
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
