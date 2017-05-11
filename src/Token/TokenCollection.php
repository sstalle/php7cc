<?php

namespace Sstalle\php7cc\Token;

class TokenCollection
{
    const TOKEN_ORIGINAL_VALUE_OFFSET = 1;

    /**
     * @var array
     */
    protected $tokens = array();

    /**
     * @param array $rawTokens Tokens as returned by token_get_all
     */
    public function __construct(array $rawTokens)
    {
        foreach ($rawTokens as $i => $rawToken) {
            if (is_array($rawToken) && count($rawToken) < 3) {
                throw new \InvalidArgumentException(sprintf('Array token at index %d has less than 3 elements', $i));
            }
        }

        $this->tokens = $rawTokens;
    }

    /**
     * @param int $tokenPosition
     *
     * @return string
     */
    public function getTokenStringValueAt($tokenPosition)
    {
        if (!isset($this->tokens[$tokenPosition])) {
            throw new \OutOfBoundsException(sprintf('Token at offset %d does not exist', $tokenPosition));
        }

        $originalToken = $this->tokens[$tokenPosition];

        return is_string($originalToken) ? $originalToken : $originalToken[static::TOKEN_ORIGINAL_VALUE_OFFSET];
    }

    /**
     * @param int    $tokenPosition
     * @param string $stringValue
     *
     * @return bool
     */
    public function isTokenEqualTo($tokenPosition, $stringValue)
    {
        return $this->getTokenStringValueAt($tokenPosition) === $stringValue;
    }

    /**
     * @param int    $tokenPosition
     * @param string $stringValue
     *
     * @return bool
     */
    public function isTokenPrecededBy($tokenPosition, $stringValue)
    {
        return $this->isNextNonWhitespaceTokenEqualTo($tokenPosition, $stringValue, false);
    }

    /**
     * @param int    $tokenPosition
     * @param string $stringValue
     *
     * @return bool
     */
    public function isTokenFollowedBy($tokenPosition, $stringValue)
    {
        return $this->isNextNonWhitespaceTokenEqualTo($tokenPosition, $stringValue, true);
    }

    /**
     * @param int    $tokenPosition
     * @param string $stringValue
     *
     * @return bool
     */
    public function isTokenEqualToOrPrecededBy($tokenPosition, $stringValue)
    {
        return $this->isTokenEqualTo($tokenPosition, $stringValue)
            || $this->isTokenPrecededBy($tokenPosition, $stringValue);
    }

    /**
     * @param int $tokenPosition
     * @param int $stringValue
     *
     * @return bool
     */
    public function isTokenEqualToOrFollowedBy($tokenPosition, $stringValue)
    {
        return $this->isTokenEqualTo($tokenPosition, $stringValue)
            || $this->isTokenFollowedBy($tokenPosition, $stringValue);
    }

    /**
     * Whitespace tokens are ignored when $stringValue is not whitespace.
     *
     * @param int    $tokenPosition
     * @param string $stringValue
     * @param bool   $scanForward   Scan forward if true, otherwise backward
     *
     * @return bool
     */
    protected function isNextNonWhitespaceTokenEqualTo($tokenPosition, $stringValue, $scanForward)
    {
        $ignoreWhitespace = !ctype_space($stringValue);

        while (isset($this->tokens[$scanForward ? ++$tokenPosition : --$tokenPosition])) {
            $currentTokenString = $this->getTokenStringValueAt($tokenPosition);
            if ($ignoreWhitespace && ctype_space($currentTokenString)) {
                continue;
            }

            return $stringValue === $currentTokenString;
        }

        return false;
    }
}
