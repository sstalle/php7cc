<?php

namespace Sstalle\php7cc\CompatibilityViolation;

use PhpParser\Node;

class Message
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
     * @var Node[]
     */
    protected $nodes;

    /**
     * @param string $text
     * @param int    $line
     * @param Node[] $nodes
     */
    public function __construct($text, $line, array $nodes)
    {
        $this->rawText = $text;
        $this->line = $line;
        $this->nodes = $nodes;
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
     * @return Node[]
     */
    public function getNodes()
    {
        return $this->nodes;
    }

    protected function generateText()
    {
        return sprintf('Line %d. %s', $this->getLine(), $this->getRawText());
    }
}
