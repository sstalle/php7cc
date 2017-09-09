<?php

namespace code\NodeAnalyzer\Reflection;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use Sstalle\php7cc\NodeAnalyzer\Reflection\FunctionLike\FunctionLikeCalleeReflector;
use Sstalle\php7cc\Reflection\Reflector\ClassReflectorInterface;
use Sstalle\php7cc\Reflection\Reflector\FunctionReflectorInterface;
use Sstalle\php7cc\Reflection\Reflector\ReflectorInterface;

class FunctionLikeCalleeReflectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider reflectorsCallNodesProvider
     *
     * @param ReflectorInterface[] $delegateReflectors
     * @param Expr                 $callNode
     * @param bool                 $isSupported
     */
    public function testSupportsCallNodesSupportedByInternalReflectors(array $delegateReflectors, $callNode, $isSupported)
    {
        $reflector = new FunctionLikeCalleeReflector($delegateReflectors);

        $this->assertSame($isSupported, $reflector->supports($callNode));
    }

    /**
     * @dataProvider reflectorsCallNodesProvider
     *
     * @param ReflectorInterface[] $delegateReflectors
     * @param Expr                 $callNode
     * @param bool                 $isSupported
     * @param string               $expectedReflectionType
     */
    public function testReflectsOnSupportedCallNodes(array $delegateReflectors, $callNode, $isSupported, $expectedReflectionType)
    {
        if (!$isSupported) {
            $this->setExpectedException('Sstalle\php7cc\NodeAnalyzer\Reflection\Exception\UnsupportedNodeTypeException');
        }

        $reflector = new FunctionLikeCalleeReflector($delegateReflectors);
        $reflection = $reflector->reflect($callNode);
        $this->assertTrue($reflection instanceof $expectedReflectionType);
    }

    /**
     * @return array
     */
    public function reflectorsCallNodesProvider()
    {
        return array_merge(
            $this->emptyReflectorsProvider(),
            $this->functionReflectorProvider(),
            $this->classReflectorProvider(),
            $this->classAndMethodReflectorProvider()
        );
    }

    /**
     * @return array
     */
    private function emptyReflectorsProvider()
    {
        return array(
            array(
                array(),
                new FuncCall(new Name('in_array')),
                false,
                'Sstalle\php7cc\Reflection\ReflectionFunctionInterface',
            ),
            array(
                array(),
                new MethodCall(new String_('unsupported'), 'method'),
                false,
                'Sstalle\php7cc\Reflection\ReflectionClassInterface',
            ),
            array(
                array(),
                new StaticCall(new Name('DateTime'), 'createFromFormat'),
                false,
                'Sstalle\php7cc\Reflection\ReflectionClassInterface',
            ),
        );
    }

    /**
     * @return array
     */
    private function functionReflectorProvider()
    {
        $inArrayReflector = $this->buildFunctionReflector(array('in_array'));
        $fooReflector = $this->buildFunctionReflector(array('foo'));

        return array(
            array(
                array($inArrayReflector),
                new FuncCall(new Name('in_array')),
                true,
                'Sstalle\php7cc\Reflection\ReflectionFunctionInterface',
            ),
            array(
                array($fooReflector),
                new FuncCall(new Name('in_array')),
                false,
                'Sstalle\php7cc\Reflection\ReflectionFunctionInterface',
            ),
            array(
                array($inArrayReflector, $fooReflector),
                new FuncCall(new Name('foo')),
                true,
                'Sstalle\php7cc\Reflection\ReflectionFunctionInterface',
            ),
            array(
                array($inArrayReflector),
                new MethodCall(new String_('unsupported'), 'method'),
                false,
                'Sstalle\php7cc\Reflection\ReflectionClassInterface',
            ),
            array(
                array($inArrayReflector),
                new StaticCall(new Name('DateTime'), 'createFromFormat'),
                false,
                'Sstalle\php7cc\Reflection\ReflectionClassInterface',
            ),
        );
    }

    /**
     * @return array
     */
    private function classReflectorProvider()
    {
        $dateTimeReflector = $this->buildClassReflector(array('DateTime'));
        $fooReflector = $this->buildClassReflector(array('Foo'));

        return array(
            array(
                array($dateTimeReflector, $fooReflector),
                new FuncCall(new Name('unsupported')),
                false,
                'Sstalle\php7cc\Reflection\ReflectionFunctionInterface',
            ),
            array(
                array($dateTimeReflector, $fooReflector),
                new MethodCall(new String_('unsupported'), 'method'),
                false,
                'Sstalle\php7cc\Reflection\ReflectionClassInterface',
            ),
            array(
                array($dateTimeReflector),
                new StaticCall(new Name('DateTime'), 'createFromFormat'),
                true,
                'Sstalle\php7cc\Reflection\ReflectionClassInterface',
            ),
            array(
                array($dateTimeReflector, $fooReflector),
                new StaticCall(new Name('Foo'), 'createFromFormat'),
                true,
                'Sstalle\php7cc\Reflection\ReflectionClassInterface',
            ),
        );
    }

    /**
     * @return array
     */
    private function classAndMethodReflectorProvider()
    {
        $inArrayReflector = $this->buildFunctionReflector(array('in_array'));
        $dateTimeReflector = $this->buildClassReflector(array('DateTime'));

        return array(
            array(
                array($dateTimeReflector, $inArrayReflector),
                new FuncCall(new Name('unsupported')),
                false,
                'Sstalle\php7cc\Reflection\ReflectionFunctionInterface',
            ),
            array(
                array($dateTimeReflector, $inArrayReflector),
                new StaticCall(new Name('DateTime'), 'createFromFormat'),
                true,
                'Sstalle\php7cc\Reflection\ReflectionClassInterface',
            ),
        );
    }

    /**
     * @param array $supportedFunctions
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|FunctionReflectorInterface
     */
    private function buildFunctionReflector(array $supportedFunctions)
    {
        $reflector = $this->getMock('Sstalle\php7cc\Reflection\Reflector\FunctionReflectorInterface');
        $reflector->method('supports')
            ->will($this->returnCallback(function ($callNode) use ($supportedFunctions) {
                return $callNode instanceof FuncCall
                    && in_array($callNode->name->toString(), $supportedFunctions, true);
            }));
        $reflector->method('reflect')
            ->willReturn($this->getMock('Sstalle\php7cc\Reflection\ReflectionFunctionInterface'));

        return $reflector;
    }

    /**
     * @param array $supportedClasses
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|ClassReflectorInterface
     */
    private function buildClassReflector(array $supportedClasses)
    {
        $reflector = $this->getMock('Sstalle\php7cc\Reflection\Reflector\ClassReflectorInterface');
        $reflector->method('supports')
            ->will($this->returnCallback(function ($callNode) use ($supportedClasses) {
                $r = $callNode instanceof StaticCall
                    && $callNode->class instanceof Name
                    && in_array($callNode->class->toString(), $supportedClasses, true);

                return $callNode instanceof StaticCall
                    && $callNode->class instanceof Name
                    && in_array($callNode->class->toString(), $supportedClasses, true);
            }));
        $reflector->method('reflect')
            ->willReturn($this->getMock('Sstalle\php7cc\Reflection\ReflectionClassInterface'));

        return $reflector;
    }
}
