<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;
use Sstalle\php7cc\Helper\NodeHelper;
use Sstalle\php7cc\Helper\RegExp\RegExpParser;

class PregReplaceEvalVisitor extends AbstractVisitor
{
    const PREG_REPLACE_EVAL_MODIFIER = 'e';

    /**
     * @var RegExpParser
     */
    protected $regExpParser;

    /**
     * @param RegExpParser $regExpParser
     */
    public function __construct(RegExpParser $regExpParser)
    {
        $this->regExpParser = $regExpParser;
    }

    public function enterNode(Node $node)
    {
        if (!NodeHelper::isFunctionCallByStaticName($node, 'preg_replace')) {
            return;
        }

        /** @var Node\Expr\FuncCall $node */
        $regExpPatternArgument = $node->args[0];
        if (!$regExpPatternArgument->value instanceof Node\Scalar\String_) {
            return;
        }

        $regExp = $this->regExpParser->parse($regExpPatternArgument->value->value);
        if ($regExp->hasModifier(static::PREG_REPLACE_EVAL_MODIFIER)) {
            $this->addContextMessage(
                sprintf('Removed regular expression modifier "%s" used', static::PREG_REPLACE_EVAL_MODIFIER),
                $node
            );
        }
    }
}
