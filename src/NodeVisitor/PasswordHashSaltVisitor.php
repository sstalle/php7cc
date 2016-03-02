<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;
use Sstalle\php7cc\NodeAnalyzer\FunctionAnalyzer;

class PasswordHashSaltVisitor extends AbstractVisitor
{
    const PASSWORD_HASH_OPTIONS_ARGUMENT_INDEX = 2;

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

    public function enterNode(Node $node)
    {
        if (!$this->functionAnalyzer->isFunctionCallByStaticName($node, array('password_hash' => true))
            || !isset($node->args[static::PASSWORD_HASH_OPTIONS_ARGUMENT_INDEX])
            || !($node->args[static::PASSWORD_HASH_OPTIONS_ARGUMENT_INDEX]->value instanceof Node\Expr\Array_)
        ) {
            return;
        }

        /** @var Node\Expr\Array_ $passwordHashOptions */
        $passwordHashOptions = $node->args[static::PASSWORD_HASH_OPTIONS_ARGUMENT_INDEX]->value;
        /** @var $node Node\Expr\FuncCall */
        foreach ($passwordHashOptions->items as $option) {
            if ($option->key instanceof Node\Scalar\String_ && $option->key->value === 'salt') {
                $this->addContextError(
                    'Deprecated option "salt" passed to password_hash function',
                    $node
                );

                break;
            }
        }
    }
}
