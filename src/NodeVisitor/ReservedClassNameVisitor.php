<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;

class ReservedClassNameVisitor extends AbstractVisitor
{

    protected $reservedClassNames = array(
        'bool',
        'int',
        'float',
        'string',
        'null',
        'false',
        'true',
        'resource',
        'object',
        'mixed',
        'numeric',
    );

    /**
     */
    public function __construct()
    {
        $this->reservedClassNames = array_flip($this->reservedClassNames);
    }


    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Class_ || $node instanceof Node\Stmt\Trait_
            || $node instanceof Node\Stmt\Interface_
        ) {
            $nodeName = strtolower($node->name);

            if (isset($this->reservedClassNames[$nodeName])) {
                $this->addContextMessage(
                    sprintf('Reserved word "%s" used as a class, interface or trait name', $nodeName),
                    $node
                );
            }
        }
    }

}