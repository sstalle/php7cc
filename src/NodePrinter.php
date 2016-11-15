<?php

namespace Sstalle\php7cc;

use PhpParser\PrettyPrinterAbstract;

class NodePrinter implements NodePrinterInterface
{
    /**
     * @var PrettyPrinterAbstract
     */
    protected $prettyPrinter;

    /**
     * @var NodeStatementsRemover
     */
    protected $nodeStatementsRemover;

    /**
     * @param PrettyPrinterAbstract $prettyPrinter
     * @param NodeStatementsRemover $nodeStatementsRemover
     */
    public function __construct(PrettyPrinterAbstract $prettyPrinter, NodeStatementsRemover $nodeStatementsRemover)
    {
        $this->prettyPrinter = $prettyPrinter;
        $this->nodeStatementsRemover = $nodeStatementsRemover;
    }

    /**
     * {@inheritdoc}
     */
    public function printNodes(array $nodes)
    {
        $nodes = $this->nodeStatementsRemover->removeInnerStatements($nodes);

        return str_replace("\n", "\n    ", $this->prettyPrinter->prettyPrint($nodes));
    }
}
