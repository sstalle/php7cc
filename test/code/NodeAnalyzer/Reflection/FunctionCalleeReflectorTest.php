<?php

namespace code\NodeAnalyzer\Reflection;

use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Name;
use Sstalle\php7cc\NodeAnalyzer\Reflection\FunctionLike\FunctionCalleeReflector;
use Sstalle\php7cc\Reflection\Reflector\FunctionReflectorInterface;

class FunctionCalleeReflectorTest extends AbstractCalleeReflectorTest
{
    /**
     * @var FunctionCalleeReflector
     */
    protected $reflector;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->reflector = new FunctionCalleeReflector($this->buildFunctionReflector());
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
            array(new FuncCall(new Name('in_array')), true),
            array(new FuncCall(new Name('unsupported')), false),
            array(new FuncCall(new Variable('foo')), false),
            array(new MethodCall(new String_('foo'), 'create'), false),
            array(new StaticCall(new Name('DateTime'), 'createFromFormat'), false),
        );
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FunctionReflectorInterface
     */
    protected function buildFunctionReflector()
    {
        $reflector = $this->getMock('Sstalle\php7cc\Reflection\Reflector\FunctionReflectorInterface');
        $reflector->method('supports')
            ->will($this->returnCallback(function ($functionName) {
                return in_array($functionName, array('in_array', 'sort'), true);
            }));
        $reflector->method('reflect')
            ->willReturn($this->getMock('Sstalle\php7cc\Reflection\ReflectionFunctionAbstractInterface'));

        return $reflector;
    }
}
