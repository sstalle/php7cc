<?php

namespace code\NodeAnalyzer;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Function_;
use Sstalle\php7cc\NodeAnalyzer\FunctionAnalyzer;
use Sstalle\php7cc\NodeAnalyzer\Reflection\FunctionLike\CalleeReflectorInterface;

class FunctionAnalyzerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FunctionAnalyzer
     */
    protected $functionAnalyzer;

    public function testIsFunctionCallByStaticNameReturnsFalseForNonFuncCallNode()
    {
        $node = new Function_('foo');
        $this->assertSame($this->functionAnalyzer->isFunctionCallByStaticName($node, 'foo'), false);
    }

    public function testIsFunctionCallByStaticNameReturnsFalseForDynamicName()
    {
        $node = new FuncCall(new Variable('foo'));
        $this->assertSame($this->functionAnalyzer->isFunctionCallByStaticName($node, 'foo'), false);
    }

    /**
     * @dataProvider isFunctionCallByStaticNameChecksLowercaseFunctionNameCorrectlyProvider
     */
    public function testIsFunctionCallByStaticNameChecksLowercaseFunctionNameCorrectly($node, $checkedFunctionNames, $result)
    {
        $this->assertSame($this->functionAnalyzer->isFunctionCallByStaticName($node, $checkedFunctionNames), $result);
    }

    /**
     * @dataProvider isFunctionCallByStaticNameChecksMixedCaseFunctionNameCorrectlyProvider
     */
    public function testIsFunctionCallByStaticNameChecksMixedCaseFunctionNameCorrectly($node, $checkedFunctionNames, $result)
    {
        $this->assertSame($this->functionAnalyzer->isFunctionCallByStaticName($node, $checkedFunctionNames), $result);
    }

    public function isFunctionCallByStaticNameChecksLowercaseFunctionNameCorrectlyProvider()
    {
        return array(
            array(
                $this->buildFuncCallNodeWithStaticName('foo'),
                'foo',
                true,
            ),
            array(
                $this->buildFuncCallNodeWithStaticName('foo'),
                array('foo' => true),
                true,
            ),
            array(
                $this->buildFuncCallNodeWithStaticName('bar'),
                array('foo' => true, 'bar' => true),
                true,
            ),
            array(
                $this->buildFuncCallNodeWithStaticName('foo'),
                'bar',
                false,
            ),
            array(
                $this->buildFuncCallNodeWithStaticName('foo'),
                array('bar' => true),
                false,
            ),
            array(
                $this->buildFuncCallNodeWithStaticName('baz'),
                array('foo' => true, 'bar' => true),
                false,
            ),
        );
    }

    public function isFunctionCallByStaticNameChecksMixedCaseFunctionNameCorrectlyProvider()
    {
        return array(
            array(
                $this->buildFuncCallNodeWithStaticName('fOo'),
                'foo',
                true,
            ),
            array(
                $this->buildFuncCallNodeWithStaticName('FoO'),
                array('foo' => true),
                true,
            ),
        );
    }

    public function testGetByReferenceCallArgumentsReturnsAllArgumentsForUnsupportedFunctions()
    {
        $callNode = $this->buildFuncCallNodeWithStaticName('foo');
        $arguments = $this->buildFuncCallArguments(2);
        $callNode->args = $arguments;

        $this->assertSame($arguments, $this->functionAnalyzer->getByReferenceCallArguments($callNode));
    }

    public function testGetByReferenceCallArgumentsReturnsNoArgumentsForFunctionsWithoutByReferenceArguments()
    {
        $callNode = $this->buildFuncCallNodeWithStaticName('in_array');
        $arguments = $this->buildFuncCallArguments(2);
        $callNode->args = $arguments;

        $this->assertSame(array(), $this->functionAnalyzer->getByReferenceCallArguments($callNode));
    }

    /**
     * @dataProvider getByReferenceCallArgumentsReturnsByReferenceArgumentsForFunctionsWithByReferenceArgumentsProvider
     *
     * @param string $functionName
     * @param int    $argumentCount
     * @param array  $byReferenceArgumentPositions
     */
    public function testGetByReferenceCallArgumentsReturnsByReferenceArgumentsForFunctionsWithByReferenceArguments(
        $functionName,
        $argumentCount,
        array $byReferenceArgumentPositions
    ) {
        $callNode = $this->buildFuncCallNodeWithStaticName($functionName);
        $arguments = $this->buildFuncCallArguments($argumentCount);
        $callNode->args = $arguments;

        $expectedByReferenceArguments = array();
        foreach ($byReferenceArgumentPositions as $position) {
            if ($position > count($arguments) - 1) {
                break;
            }

            $expectedByReferenceArguments[$position] = $arguments[$position];
        }

        $this->assertSame($expectedByReferenceArguments, $this->functionAnalyzer->getByReferenceCallArguments($callNode));
    }

    /**
     * @return array
     */
    public function getByReferenceCallArgumentsReturnsByReferenceArgumentsForFunctionsWithByReferenceArgumentsProvider()
    {
        return array(
            array('in_array', 0, array()),
            array('in_array', 2, array()),
            array('sort', 0, array(0)),
            array('sort', 2, array(0)),
            array('preg_match', 1, array(2)),
            array('preg_match', 4, array(2)),
        );
    }

    /**
     * @param string $name
     *
     * @return FuncCall
     */
    protected function buildFuncCallNodeWithStaticName($name)
    {
        return new FuncCall(new Name(array($name)));
    }

    /**
     * @param int $argumentCount
     *
     * @return array
     */
    protected function buildFuncCallArguments($argumentCount)
    {
        $arguments = array();
        for ($i = 0; $i < $argumentCount; ++$i) {
            $arguments[] = new Arg(new String_('test'));
        }

        return $arguments;
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $reflector = $this->buildReflector();

        $this->functionAnalyzer = new FunctionAnalyzer($reflector);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|CalleeReflectorInterface
     */
    protected function buildReflector()
    {
        $byReferenceArgumentPositionsByFunction = array(
            'in_array' => array(),
            'sort' => array(0),
            'preg_match' => array(2),
        );

        $reflector = $this->getMock('Sstalle\php7cc\NodeAnalyzer\Reflection\FunctionLike\CalleeReflectorInterface');
        $reflector->method('supports')
            ->will($this->returnCallback(function (FuncCall $callNode) use ($byReferenceArgumentPositionsByFunction) {
                return in_array($callNode->name->toString(), array_keys($byReferenceArgumentPositionsByFunction));
            }));

        $self = $this;
        $reflector->method('reflect')
            ->will($this->returnCallback(function (FuncCall $callNode) use ($self, $byReferenceArgumentPositionsByFunction) {
                $functionName = $callNode->name->toString();
                $functionReflection = $self->getMock(
                    'Sstalle\php7cc\NodeAnalyzer\Reflection\ReflectionFunctionInterface',
                    array('getByReferenceParameterPositions')
                );
                $functionReflection->method('getByReferenceParameterPositions')
                    ->will($self->returnCallback(function () use ($functionName, $byReferenceArgumentPositionsByFunction) {
                        return isset($byReferenceArgumentPositionsByFunction[$functionName])
                            ? $byReferenceArgumentPositionsByFunction[$functionName]
                            : array();
                    }));

                return $functionReflection;
            }));

        return $reflector;
    }
}
