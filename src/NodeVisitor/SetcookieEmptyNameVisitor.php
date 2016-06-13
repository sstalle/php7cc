<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;
use Sstalle\php7cc\CompatibilityViolation\Message;
use Sstalle\php7cc\NodeAnalyzer\FunctionAnalyzer;

class SetcookieEmptyNameVisitor extends AbstractVisitor
{
    const LEVEL = Message::LEVEL_ERROR;

    /**
     * @var array
     */
    protected static $setcookieFamilyFunctions = array(
        'setcookie' => true,
        'setrawcookie' => true,
    );

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
    }

    /**
     * {@inheritdoc}
     */
    public function enterNode(Node $node)
    {
        if (!$this->functionAnalyzer->isFunctionCallByStaticName($node, self::$setcookieFamilyFunctions)) {
            return;
        }

        /** @var Node\Expr\FuncCall $node */
        $cookieNameArgumentValue = isset($node->args[0]) ? $node->args[0]->value : null;
        $isEmptyString = $cookieNameArgumentValue && $cookieNameArgumentValue instanceof Node\Scalar\String_
            && $cookieNameArgumentValue->value === '';
        $isEmptyConstant = $cookieNameArgumentValue && $cookieNameArgumentValue instanceof Node\Expr\ConstFetch
            && in_array(strtolower($cookieNameArgumentValue->name->toString()), array('null', 'false'), true);

        if ($isEmptyConstant || $isEmptyString) {
            $this->addContextMessage(
                sprintf('Function "%s" called with an empty cookie name', $node->name->toString()),
                $node
            );
        }
    }
}
