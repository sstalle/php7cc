<?php

namespace Sstalle\php7cc\Helper\RegExp;

class RegExpParser
{

    protected static $delimiterPairs = array(
        '(' => ')',
        '[' => ']',
        '{' => '}',
        '<' => '>',
    );

    /**
     * @param string $regExp
     * @return RegExp
     */
    public function parse($regExp)
    {
        if (!$regExp) {
            throw new \InvalidArgumentException('RegExp is empty');
        }

        $startDelimiter = $regExp[0];
        $endDelimiter = isset(static::$delimiterPairs[$startDelimiter])
            ? static::$delimiterPairs[$startDelimiter]
            : $startDelimiter;
        $endDelimiterPosition = strrpos($regExp, $endDelimiter);
        if (!$endDelimiterPosition) {
            throw new \InvalidArgumentException(sprintf('Closing delimiter %s not found', $startDelimiter));
        }

        return new RegExp(
            $startDelimiter,
            $endDelimiter,
            substr($regExp, 1, $endDelimiterPosition - 1),
            substr($regExp, $endDelimiterPosition + 1)
        );
    }

}