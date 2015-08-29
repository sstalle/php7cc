<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;

class NewClassVisitor extends AbstractVisitor
{
    private static $newClasses = array(
        'IntlChar',

        'ReflectionGenerator',
        'ReflectionType',

        'SessionUpdateTimestampHandlerInterface',

        'Throwable',
        'Error',
        'TypeError',
        'ParseError',
        'AssertionError',
        'ArithmeticError',
        'DivisionByZeroError',
    );

    private static $lowerCasedNewClasses = array();

    public function __construct()
    {
        foreach (self::$newClasses as $className) {
            self::$lowerCasedNewClasses[strtolower($className)] = $className;
        }
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\ClassLike
            && isset($node->namespacedName) // Property set by the NameResolver visitor
            && count($node->namespacedName->parts) === 1
            && ($lowerCasedClassName = strtolower($node->name))
            && array_key_exists($lowerCasedClassName, self::$lowerCasedNewClasses)) {
            $this->addContextMessage(
                sprintf(
                    'Class/trait/interface "%s" was added in the global namespace',
                    self::$lowerCasedNewClasses[$lowerCasedClassName]
                ),
                $node
            );
        }
    }
}
