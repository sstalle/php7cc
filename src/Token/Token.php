<?php

namespace Sstalle\php7cc\Token;

class Token
{
    const ORIGINAL_VALUE_OFFSET = 1;

    /**
     * Token as returned by token_get_all.
     *
     * @var array|string
     */
    protected $originalToken;

    /**
     * @param array|string $originalToken
     */
    public function __construct($originalToken)
    {
        if (is_array($originalToken) && count($originalToken) < 3) {
            throw new \InvalidArgumentException(sprintf('Array token has less than 3 elements'));
        }

        $this->originalToken = $originalToken;
    }

    /**
     * @param string $stringValue
     *
     * @return bool
     */
    public function isStringValueEqualTo($stringValue)
    {
        return $this->__toString() === $stringValue;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return is_string($this->originalToken)
            ? $this->originalToken
            : $this->originalToken[static::ORIGINAL_VALUE_OFFSET];
    }
}
