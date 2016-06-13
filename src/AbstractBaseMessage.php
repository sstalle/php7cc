<?php

namespace Sstalle\php7cc;

abstract class AbstractBaseMessage
{
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
