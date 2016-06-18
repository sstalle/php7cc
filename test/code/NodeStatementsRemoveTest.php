<?php

namespace code;

use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Case_;
use PhpParser\Node\Stmt\Switch_;
use PhpParser\NodeAbstract;
use Sstalle\php7cc\NodeStatementsRemover;

class NodeStatementsRemoveTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NodeStatementsRemover
     */
    protected $statementsRemover;

    public function setUp()
    {
        $this->statementsRemover = new NodeStatementsRemover();
    }

    public function testRemoveNodeStatements()
    {
        $node = new Node();
        $node->stmts = array(1);

        $result = $this->statementsRemover->removeInnerStatements(array($node), true);
        $this->assertCount(1, $node->stmts);
        $this->assertCount(0, $result[0]->stmts);

        $result = $this->statementsRemover->removeInnerStatements(array($node), false);
        $this->assertCount(0, $node->stmts);
        $this->assertCount(0, $result[0]->stmts);
    }

    public function testRemoveSwitchCases()
    {
        $node = new Switch_(new Variable('test'), array(new Case_(new Variable('test'))));

        $result = $this->statementsRemover->removeInnerStatements(array($node), true);
        $this->assertCount(0, $result[0]->cases);
        $this->assertCount(1, $node->cases);

        $result = $this->statementsRemover->removeInnerStatements(array($node), false);
        $this->assertCount(0, $result[0]->cases);
        $this->assertCount(0, $node->cases);
    }
}

class Node extends NodeAbstract
{
    public $stmts;
}
