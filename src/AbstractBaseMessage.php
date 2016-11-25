<?php

namespace Sstalle\php7cc;

abstract class AbstractBaseMessage
{
    const LEVEL_INFO = 0;
    const LEVEL_WARNING = 1;
    const LEVEL_ERROR = 2;
    const LEVEL_PARSE_ERROR = 3;

    const LEVEL_NAME_INFO = 'info';
    const LEVEL_NAME_WARNING = 'warning';
    const LEVEL_NAME_ERROR = 'error';
    const LEVEL_NAME_PARSE_ERROR = 'parse_error';

    private static $levelNames = array(
        self::LEVEL_INFO => self::LEVEL_NAME_INFO,
        self::LEVEL_WARNING => self::LEVEL_NAME_WARNING,
        self::LEVEL_ERROR => self::LEVEL_NAME_ERROR,
        self::LEVEL_PARSE_ERROR => self::LEVEL_NAME_PARSE_ERROR,
    );

    /**
     * @var string
     */
    protected $rawText;

    /**
     * @var string
     */
    protected $text;

    /**
     * @var int
     */
    protected $line;

    /**
     * @param string   $text
     * @param int|null $line
     */
    public function __construct($text, $line = null)
    {
        $this->rawText = $text;
        $this->line = $line;
        $this->text = $this->generateText();
    }

    /**
     * @return int
     */
    abstract public function getLevel();

    /**
     * @return string
     */
    public function getLevelName()
    {
        $level = $this->getLevel();
        if (!isset(self::$levelNames[$level])) {
            throw new \UnexpectedValueException(sprintf('Unknown message level %d', $level));
        }

        return self::$levelNames[$level];
    }

    /**
     * @return string
     */
    public function getRawText()
    {
        return $this->rawText;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return int
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @return string
     */
    abstract protected function generateText();
}
