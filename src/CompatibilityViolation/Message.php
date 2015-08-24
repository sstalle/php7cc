<?php

namespace Sstalle\php7cc\CompatibilityViolation;

use PhpParser\Node;
use Sstalle\php7cc\AbstractBaseMessage;

class Message extends AbstractBaseMessage
{
    /**
     * @var Node[]
     */
    protected $nodes;

    /**
     * @param string   $text
     * @param int|null $line
     * @param Node[]   $nodes
     */
    public function __construct($text, $line = null, array $nodes = array())
    {
        parent::__construct($text, $line);
        $this->nodes = $nodes;
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
