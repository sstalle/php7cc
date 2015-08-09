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
    protected $modifiers;

    /**
     * @param string $delimiter
     * @param string $expression
     * @param string $modifiers
     */
    public function __construct($delimiter, $expression, $modifiers)
    {
        if (!$delimiter) {
            throw new \InvalidArgumentException('Delimiter must not be empty');
        }

        $this->delimiter = $delimiter;
        $this->expression = $expression;
        $this->modifiers = $modifiers;
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
    public function getModifiers()
    {
        return $this->modifiers;
    }

    /**
     * @param string $modifier
     * @return bool
     */
    public function hasModifier($modifier)
    {
        return strpos($this->getModifiers(), $modifier) !== false;
    }

}