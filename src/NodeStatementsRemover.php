<?php

namespace Sstalle\php7cc;

use PhpParser\Node;

class NodeStatementsRemover
{
    /**
     * @param Node[] $nodes
     * @param bool   $cloneNodes
     *
     * @return \PhpParser\Node[]
     */
    public function removeInnerStatements($nodes, $cloneNodes = true)
    {
        $resultNodes = array();

        foreach ($nodes as $node) {
            if ($cloneNodes) {
                $node = clone $node;
            }

            if (property_exists($node, 'stmts')) {
                $node->stmts = array();
            }

            if ($node instanceof Node\Stmt\Switch_) {
                $node->cases = array();
            }

            $resultNodes[] = $node;
        }

        return $resultNodes;
    }
}
