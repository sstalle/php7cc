<?php

namespace Sstalle\php7cc\CompatibilityViolation;

use PhpParser\Node;
use Sstalle\php7cc\AbstractBaseMessage;

class Message extends AbstractBaseMessage
{
    const LEVEL_INFO = 0;
    const LEVEL_WARNING = 1;
    const LEVEL_ERROR = 2;

    /**
     * @var int
     */
    protected $level;

    /**
     * @var Node[]
     */
    protected $nodes;

    /**
     * @param string   $text
     * @param int|null $line
     * @param int      $level
     * @param Node[]   $nodes
     */
    public function __construct($text, $line = null, $level = self::LEVEL_INFO, array $nodes = array())
    {
        parent::__construct($text, $line);
        $this->level = $level;
        $this->nodes = $nodes;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @return Node[]
     */
    public function getNodes()
    {
        return $this->nodes;
    }

    /**
     * {@inheritdoc}
     */
    protected function generateText()
    {
        return sprintf('Line %d. %s', $this->getLine(), $this->getRawText());
    }
}
