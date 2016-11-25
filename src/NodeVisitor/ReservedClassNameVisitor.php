<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;
use Sstalle\php7cc\CompatibilityViolation\Message;
use Sstalle\php7cc\NodeAnalyzer\FunctionAnalyzer;

class ReservedClassNameVisitor extends AbstractVisitor
{
    const LEVEL = Message::LEVEL_ERROR;
    const RESERVED_NAME_MESSAGE = 'Reserved name "%s" used %s ';
    const FUTURE_RESERVED_NAME_MESSAGE = <<<'MSG'
Name "%s" that is reserved for future use (does not cause an error in PHP 7) used %s
MSG;

    /**
     * @var string[]
     */
    protected $reservedClassNames = array(
        'bool',
        'int',
        'float',
        'string',
        'null',
        'false',
        'true',
    );

    /**
     * @var string[]
     */
    protected $futureReservedClassNames = array(
        'resource',
        'object',
        'mixed',
        'numeric',
    );

    /**
     * @var array
     */
    protected $reservedNamesToMessagesMap = array();

    /**
     * @var FunctionAnalyzer
     */
    protected $functionAnalyzer;

    /**
     * @param FunctionAnalyzer $functionAnalyzer
     */
    public function __construct(FunctionAnalyzer $functionAnalyzer)
    {
        $this->functionAnalyzer = $functionAnalyzer;
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
        $checkedName = '';
        $usagePatternName = null;

        if ($node instanceof Node\Stmt\ClassLike) {
            $checkedName = $node->name;
            $usagePatternName = 'as a class, interface or trait name';
        } elseif ($this->functionAnalyzer->isFunctionCallByStaticName($node, 'class_alias')) {
            /** @var Node\Expr\FuncCall $node */
            $secondArgument = isset($node->args[1]) ? $node->args[1] : null;

            if (!$secondArgument || !$secondArgument->value instanceof Node\Scalar\String_) {
                return;
            }

            $checkedName = $secondArgument->value->value;
            $usagePatternName = 'as a class alias';
        } elseif ($node instanceof Node\Stmt\UseUse) {
            $checkedName = $node->alias;
            $usagePatternName = 'as a use statement alias';
        }

        $checkedName = strtolower($checkedName);
        if ($checkedName && isset($this->reservedNamesToMessagesMap[$checkedName])) {
            $this->addContextMessage(
                sprintf($this->reservedNamesToMessagesMap[$checkedName], $checkedName, $usagePatternName),
                $node
            );
        }
    }
}
