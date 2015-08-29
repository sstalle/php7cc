<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;

class PHP4ConstructorVisitor extends AbstractVisitor
{
    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Class_) {
            $currentClassName = $node->name;
            $hasPhp4Constructor = false;
            $hasPhp5Constructor = false;
            $php4ConstructorNode = null;

            // Checks if class is namespaced (property namespacedName was set by the NameResolver visitor)
            if (count($node->namespacedName->parts) > 1) {
                return;
            }

            foreach ($node->stmts as $stmt) {
                // Check for constructors
                if ($stmt instanceof Node\Stmt\ClassMethod) {
                    if ($stmt->name === '__construct') {
                        $hasPhp5Constructor = true;
                    }

                    if ($stmt->name === $currentClassName) {
                        $hasPhp4Constructor = true;
                        $php4ConstructorNode = $stmt;
                    }
                }
            }

            if ($hasPhp4Constructor && !$hasPhp5Constructor) {
                $this->addContextMessage(
                    'PHP 4 constructors are now deprecated',
                    $php4ConstructorNode
                );
            }
        }
    }
}
