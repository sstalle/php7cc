<?php

namespace Sstalle\php7cc;

use PhpParser\Node;

interface NodePrinterInterface
{
    /**
     * @param Node[] $nodes
     *
     * @return string
     */
    public function printNodes(array $nodes);
}
