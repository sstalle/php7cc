<?php

namespace Sstalle\php7cc\Helper\RegExp;

class RegExpParser
{

    /**
     * @param string $regExp
     * @return RegExp
     */
    public function parse($regExp)
    {
        if (!$regExp) {
            throw new \InvalidArgumentException('RegExp is empty');
        }

        $delimiter = $regExp[0];
        if (preg_match('/[a-z0-9\s+]/', $delimiter) === 1) {
            throw new \InvalidArgumentException(sprintf('Invalid delimiter %s used', $delimiter));
        }

        $endDelimiterPosition = strrpos($regExp, $delimiter);
        if (!$endDelimiterPosition) {
            throw new \InvalidArgumentException(sprintf('Closing delimiter %s not found', $delimiter));
        }

        return new RegExp(
            $delimiter,
            substr($regExp, 1, $endDelimiterPosition - 1),
            substr($regExp, $endDelimiterPosition + 1)
        );
    }

}