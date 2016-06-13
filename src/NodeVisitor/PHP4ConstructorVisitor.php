<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;
use Sstalle\php7cc\CompatibilityViolation\Message;

class PHP4ConstructorVisitor extends AbstractVisitor
{
    const LEVEL = Message::LEVEL_ERROR;

    /**
     * {@inheritdoc}
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Class_) {
            $currentClassName = $node->name;
            $hasPhp4Constructor = false;
            $hasPhp5Constructor = false;
            $php4ConstructorNode = null;

            // Anonymous class can't use php4 constructor by definition
            if (empty($currentClassName)) {
                return;
            }

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
