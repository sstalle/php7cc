<?php

namespace code\NodeAnalyzer;

use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Function_;
use Sstalle\php7cc\NodeAnalyzer\FunctionAnalyzer;

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
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->functionAnalyzer = new FunctionAnalyzer();
    }
}
