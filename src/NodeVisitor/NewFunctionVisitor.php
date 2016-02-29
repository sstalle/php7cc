<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;

class NewFunctionVisitor extends AbstractVisitor
{
    private static $newFunctions = array(
        'random_bytes',
        'random_int',

        'error_clear_last',

        'gmp_random_seed',

        'intdiv',

        'preg_replace_callback_array',

        'posix_setrlimit',

        'inflate_add',
        'inflate_init',
        'deflate_add',
        'deflate_init',
    );

    private static $lowerCasedNewFunctions = array();

    public function __construct()
    {
        foreach (self::$newFunctions as $function) {
            self::$lowerCasedNewFunctions[strtolower($function)] = $function;
        }
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Function_
            && ($lowerCasedFunction = strtolower($node->name))
            && array_key_exists($lowerCasedFunction, self::$lowerCasedNewFunctions)) {
            if (isset($node->namespacedName) && count($node->namespacedName->parts) === 1) {
                $this->addContextError(
                    sprintf(
                        'Cannot redeclare global function "%s"',
                        self::$lowerCasedNewFunctions[$lowerCasedFunction]
                    ),
                    $node
                );
            } else {
                $this->addContextWarning(
                    sprintf(
                        'Your namespaced function "%s" could replace the new global function added in PHP 7',
                        self::$lowerCasedNewFunctions[$lowerCasedFunction]
                    ),
                    $node
                );
            }
        }
    }
}
