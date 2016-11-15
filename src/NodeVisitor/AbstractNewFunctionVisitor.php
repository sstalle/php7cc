<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;

abstract class AbstractNewFunctionVisitor extends AbstractVisitor
{
    /**
     * @var string[]
     */
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

    /**
     * @var string[]
     */
    private static $lowerCasedNewFunctions = array();

    public function __construct()
    {
        foreach (self::$newFunctions as $function) {
            self::$lowerCasedNewFunctions[strtolower($function)] = $function;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Function_
            && ($lowerCasedFunction = strtolower($node->name))
            && array_key_exists($lowerCasedFunction, self::$lowerCasedNewFunctions)
            && $this->accepts($node)
        ) {
            $this->addContextMessage($this->getMessageText(self::$lowerCasedNewFunctions[$lowerCasedFunction]), $node);
        }
    }

    /**
     * @param Node\Stmt\Function_ $node
     *
     * @return bool
     */
    abstract protected function accepts(Node\Stmt\Function_ $node);

    /**
     * @param string $functionName
     *
     * @return string
     */
    abstract protected function getMessageText($functionName);
}
