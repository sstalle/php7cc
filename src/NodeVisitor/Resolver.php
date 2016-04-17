<?php

namespace Sstalle\php7cc\NodeVisitor;

use Sstalle\php7cc\CompatibilityViolation\Message;

class Resolver implements ResolverInterface
{
    /**
     * @var VisitorInterface[]
     */
    protected $visitors = array();

    /**
     * @var int
     */
    protected $level;

    /**
     * @param array $visitors
     * @param int   $level
     */
    public function __construct($visitors = array(), $level = Message::LEVEL_INFO)
    {
        foreach ($visitors as $visitor) {
            $this->addVisitor($visitor);
        }
        $this->level = $level;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve()
    {
        $level = $this->level;

        return array_values(array_filter($this->visitors, function (VisitorInterface $visitor) use ($level) {
            return $visitor->getLevel() >= $level;
        }));
    }

    /**
     * @param int $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * @param VisitorInterface $visitor
     */
    protected function addVisitor(VisitorInterface $visitor)
    {
        $this->visitors[] = $visitor;
    }
}
