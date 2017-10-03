<?php

namespace Sstalle\php7cc\Lexer;

use PhpParser\Lexer;
use PhpParser\Parser\Tokens;

class ExtendedLexer extends Lexer\Emulative
{
    /**
     * {@inheritdoc}
     */
    public function getNextToken(&$value = null, &$startAttributes = null, &$endAttributes = null)
    {
        $tokenId = parent::getNextToken($value, $startAttributes, $endAttributes);

        if ($tokenId == Tokens::T_CONSTANT_ENCAPSED_STRING // non-interpolated string
            || $tokenId == Tokens::T_LNUMBER               // integer
            || $tokenId == Tokens::T_DNUMBER               // floating point number
        ) {
            // could also use $startAttributes, doesn't really matter here
            $endAttributes['originalValue'] = $value;
        }

        if ($tokenId == Tokens::T_CONSTANT_ENCAPSED_STRING) {
            $endAttributes['isDoubleQuoted'] = $value[0] === '"';
        }

        if ($tokenId == Tokens::T_END_HEREDOC) {
            $endAttributes['isHereDoc'] = true;
        }

        return $tokenId;
    }
}
