<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;

class ReservedClassNameVisitor extends AbstractVisitor
{

    const RESERVED_NAME_MESSAGE = 'Reserved name "%s" used %s ';
    const FUTURE_RESERVED_NAME_MESSAGE = <<<MSG
Name "%s" that is reserved for future use (does not cause an error in PHP 7) used %s
MSG;

    protected $reservedClassNames = array(
        'bool',
        'int',
        'float',
        'string',
        'null',
        'false',
        'true',
    );

    protected $futureReservedClassNames = array(
        'resource',
        'object',
        'mixed',
        'numeric',
    );

    protected $reservedNamesToMessagesMap = array();

    /**
     */
    public function __construct()
    {
        $this->reservedNamesToMessagesMap = array_merge(
            array_fill_keys(
                $this->reservedClassNames,
                static::RESERVED_NAME_MESSAGE
            ),
            array_fill_keys(
                $this->futureReservedClassNames,
                static::FUTURE_RESERVED_NAME_MESSAGE
            )
        );
    }


    public function enterNode(Node $node)
    {
        $nodeName = null;
        $usagePatternName = null;

        if ($node instanceof Node\Stmt\ClassLike) {
            $nodeName = strtolower($node->name);
            $usagePatternName = 'as a class, interface or trait name';
        }

        if ($nodeName && isset($this->reservedNamesToMessagesMap[$nodeName])) {
            $this->addContextMessage(
                sprintf($this->reservedNamesToMessagesMap[$nodeName], $nodeName, $usagePatternName),
                $node
            );
        }
    }

}