<?php

namespace code\NodeAnalyzer\Reflection;

use PhpParser\Node\Expr;
use Sstalle\php7cc\NodeAnalyzer\Reflection\FunctionLike\FunctionCalleeReflector;
use Sstalle\php7cc\NodeAnalyzer\Reflection\FunctionLike\MethodCalleeReflector;
use Sstalle\php7cc\Reflection\ReflectionFunctionAbstractInterface;

abstract class AbstractCalleeReflectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return FunctionCalleeReflector|MethodCalleeReflector
     */
    abstract public function getReflector();

    /**
     * @return array
     */
    abstract public function callNodesProvider();

    /**
     * @dataProvider callNodesProvider
     *
     * @param Expr $callNode
     * @param bool $isSupported
     */
    public function testSupportsOnlyStaticNameFunctionsSupportedByInternalReflector($callNode, $isSupported)
    {
        $this->assertSame($isSupported, $this->getReflector()->supports($callNode));
    }

    /**
     * @dataProvider callNodesProvider
     *
     * @param Expr $callNode
     * @param bool $isSupported
     */
    public function testReflectsOnSupportedCallNodes($callNode, $isSupported)
    {
        if (!$isSupported) {
            $this->setExpectedException('Sstalle\php7cc\NodeAnalyzer\Reflection\Exception\UnsupportedNodeTypeException');
        }

        $reflection = $this->getReflector()->reflect($callNode);
        $this->assertTrue($reflection instanceof ReflectionFunctionAbstractInterface);
    }
}
