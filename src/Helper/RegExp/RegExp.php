<?php

namespace Sstalle\php7cc\Helper\RegExp;

class RegExp
{
    /**
     * @var string[string]
     */
    protected static $delimiterPairs = array(
        '(' => ')',
        '[' => ']',
        '{' => '}',
        '<' => '>',
    );

    /**
     * @var string
     */
    protected $startDelimiter;

    /**
     * @var string
     */
    protected $endDelimiter;

    /**
     * @var string
     */
    protected $expression;

    /**
     * @var string
     */
    protected $modifiers;

    /**
     * @param string $startDelimiter
     * @param string $endDelimiter
     * @param string $expression
     * @param string $modifiers
     */
    public function __construct($startDelimiter, $endDelimiter, $expression, $modifiers)
    {
        if (!$startDelimiter || !$endDelimiter) {
            throw new \InvalidArgumentException('Delimiter must not be empty');
        }

        foreach (array($startDelimiter, $endDelimiter) as $delimiter) {
            if (preg_match('/[\\\\a-z0-9\s+]/', strtolower($delimiter)) === 1) {
                throw new \InvalidArgumentException(sprintf('Invalid delimiter %s used', $startDelimiter));
            }
        }

        $hasPairedDelimiter = isset(static::$delimiterPairs[$startDelimiter]);
        if (($hasPairedDelimiter && static::$delimiterPairs[$startDelimiter] !== $endDelimiter)
            || (!$hasPairedDelimiter && $startDelimiter !== $endDelimiter)
        ) {
            throw new \InvalidArgumentException(
                sprintf('Start delimiter %s does not match end delimiter %s', $startDelimiter, $endDelimiter)
            );
        }

        $this->startDelimiter = $startDelimiter;
        $this->endDelimiter = $endDelimiter;
        $this->expression = $expression;
        $this->modifiers = $modifiers;
    }

    /**
     * @return string
     */
    public function getStartDelimiter()
    {
        return $this->startDelimiter;
    }

    /**
     * @return string
     */
    public function getEndDelimiter()
    {
        return $this->endDelimiter;
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
     *
     * @return bool
     */
    public function hasModifier($modifier)
    {
        return strpos($this->getModifiers(), $modifier) !== false;
    }

    /**
     * @return string
     */
    public static function getDelimiterPairs()
    {
        return self::$delimiterPairs;
    }
}
