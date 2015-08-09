<?php

namespace Sstalle\php7cc\Helper\RegExp;

class RegExp
{

    /**
     * @var string
     */
    protected $delimiter;

    /**
     * @var string
     */
    protected $expression;

    /**
     * @var string
     */
    protected $flags;

    /**
     * @param string $delimiter
     * @param string $expression
     * @param string $flags
     */
    public function __construct($delimiter, $expression, $flags)
    {
        if (!$delimiter) {
            throw new \InvalidArgumentException('Delimiter must not be empty');
        }

        $this->delimiter = $delimiter;
        $this->expression = $expression;
        $this->flags = $flags;
    }

    /**
     * @return string
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }

    /**
     * @return string
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * @return string
     */
    public function getFlags()
    {
        return $this->flags;
    }

    /**
     * @param string $flag
     * @return bool
     */
    public function hasFlag($flag)
    {
        return strpos($this->getFlags(), $flag) !== false;
    }

}