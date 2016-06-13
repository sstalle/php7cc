<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;
use Sstalle\php7cc\CompatibilityViolation\Message;
use Sstalle\php7cc\NodeAnalyzer\FunctionAnalyzer;
use Sstalle\php7cc\Helper\RegExp\RegExpParser;

class PregReplaceEvalVisitor extends AbstractVisitor
{
    const LEVEL = Message::LEVEL_ERROR;
    const PREG_REPLACE_EVAL_MODIFIER = 'e';

    /**
     * @var RegExpParser
     */
    protected $regExpParser;

    /**
     * @var FunctionAnalyzer
     */
    protected $functionAnalyzer;

    /**
     * @param RegExpParser     $regExpParser
     * @param FunctionAnalyzer $functionAnalyzer
     */
    public function __construct(RegExpParser $regExpParser, FunctionAnalyzer $functionAnalyzer)
    {
        $this->regExpParser = $regExpParser;
        $this->functionAnalyzer = $functionAnalyzer;
    }

    /**
     * {@inheritdoc}
     */
    public function enterNode(Node $node)
    {
        if (!$this->functionAnalyzer->isFunctionCallByStaticName($node, 'preg_replace')) {
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
