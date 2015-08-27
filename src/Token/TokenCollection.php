<?php

namespace Sstalle\php7cc\Token;

class TokenCollection
{
    /**
     * @var Token[]
     */
    protected $tokens = array();

    /**
     * @param array $rawTokens Tokens as returned by token_get_all
     */
    public function __construct(array $rawTokens)
    {
        foreach ($rawTokens as $rawToken) {
            $this->tokens[] = new Token($rawToken);
        }
    }

    /**
     * @param int $tokenPosition
     *
     * @return Token
     */
    public function getToken($tokenPosition)
    {
        if (!isset($this->tokens[$tokenPosition])) {
            throw new \OutOfBoundsException(sprintf('Token at offset %d does not exist', $tokenPosition));
        }

        return $this->tokens[$tokenPosition];
    }

    /**
     * @param int    $tokenPosition
     * @param string $stringValue
     *
     * @return bool
     */
    public function isTokenEqualTo($tokenPosition, $stringValue)
    {
        return $this->getToken($tokenPosition)->isStringValueEqualTo($stringValue);
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
            $currentToken = $this->tokens[$tokenPosition];
            if ($ignoreWhitespace && ctype_space($currentToken->__toString())) {
                continue;
            }

            return $currentToken->isStringValueEqualTo($stringValue);
        }

        return false;
    }
}
