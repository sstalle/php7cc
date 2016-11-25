<?php

namespace code;

use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\PrettyPrinter\Standard;
use Sstalle\php7cc\NodePrinter;
use Sstalle\php7cc\NodeStatementsRemover;

class NodePrinterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NodePrinter
     */
    private $printer;

    /**
     * @dataProvider printNodesProvider
     */
    public function testPrintNodes(array $nodes, $printedCode)
    {
        $this->assertSame($printedCode, $this->printer->printNodes($nodes));
    }

    public function printNodesProvider()
    {
        return array(
            array(
                array(new Class_('test')),
                "class test\n    {\n    }",
            ),
            array(
                array(new Array_()),
                'array();',
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->printer = new NodePrinter(new Standard(), new NodeStatementsRemover());
    }
}
