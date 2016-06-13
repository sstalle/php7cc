<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;
use Sstalle\php7cc\CompatibilityViolation\Message;

class NewClassVisitor extends AbstractVisitor
{
    const LEVEL = Message::LEVEL_ERROR;

    /**
     * @var string[]
     */
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

    /**
     * @var string[]
     */
    private static $lowerCasedNewClasses = array();

    public function __construct()
    {
        foreach (self::$newClasses as $className) {
            self::$lowerCasedNewClasses[strtolower($className)] = $className;
        }
    }

    /**
     * {@inheritdoc}
     */
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
