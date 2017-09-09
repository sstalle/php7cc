<?php

namespace code\NodeAnalyzer\Reflection;

use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use Sstalle\php7cc\NodeAnalyzer\Reflection\FunctionLike\MethodCalleeReflector;
use Sstalle\php7cc\Reflection\Reflector\ClassReflectorInterface;

class MethodCalleeReflectorTest extends AbstractCalleeReflectorTest
{
    /**
     * @var MethodCalleeReflector
     */
    protected $reflector;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->reflector = new MethodCalleeReflector($this->buildClassReflector());
    }

    /**
     * {@inheritdoc}
     */
    public function getReflector()
    {
        return $this->reflector;
    }

    /**
     * {@inheritdoc}
     */
    public function callNodesProvider()
    {
        return array(
            array(new FuncCall(new Name('unsupported')), false),
            array(new MethodCall(new String_('unsupported'), 'method'), false),
            array(new StaticCall(new Name('DateTime'), 'createFromFormat'), true),
            array(new StaticCall(new Name('UnsupportedClass'), 'test'), false),
            array(new StaticCall(new Variable('foo'), 'test'), false),
        );
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ClassReflectorInterface
     */
    protected function buildClassReflector()
    {
        $reflector = $this->getMock('Sstalle\php7cc\Reflection\Reflector\ClassReflectorInterface');
        $reflector->method('supports')
            ->will($this->returnCallback(function ($className) {
                return $className === 'DateTime';
            }));
        $self = $this;
        $reflector->method('reflect')
            ->will($this->returnCallback(function () use ($self) {
                $classReflector = $self->getMock('Sstalle\php7cc\Reflection\ReflectionClassInterface');
                $classReflector->method('getMethod')
                    ->willReturn($self->getMock('Sstalle\php7cc\Reflection\ReflectionMethodInterface'));

                return $classReflector;
            }));

        return $reflector;
    }
}
