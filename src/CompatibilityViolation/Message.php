<?php

namespace Sstalle\php7cc\CompatibilityViolation;

use PhpParser\Node;

class Message
{

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
     * @param int $line
     * @param Node[] $nodes
     */
    public function __construct($text, $line, array $nodes)
    {
        $this->text = $text;
        $this->line = $line;
        $this->nodes = $nodes;
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

}