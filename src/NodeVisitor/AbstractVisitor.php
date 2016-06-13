<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use Sstalle\php7cc\CompatibilityViolation\ContextInterface;
use Sstalle\php7cc\CompatibilityViolation\Message;
use Sstalle\php7cc\Token\TokenCollection;

abstract class AbstractVisitor extends NodeVisitorAbstract implements VisitorInterface
{
    const LEVEL = Message::LEVEL_INFO;

    /**
     * @var ContextInterface
     */
    protected $context;

    /**
     * @var TokenCollection
     */
    protected $tokenCollection;

    protected $tokens;

    /**
     * @param ContextInterface $context
     */
    public function initializeContext(ContextInterface $context)
    {
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function setTokenCollection(TokenCollection $tokenCollection)
    {
        $this->tokenCollection = $tokenCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel()
    {
        return static::LEVEL;
    }

    /**
     * @param string $text
     * @param Node   $node
     */
    protected function addContextMessage($text, Node $node)
    {
        $this->context->addMessage(new Message($text, $node->getAttribute('startLine'), $this->getLevel(), array($node)));
    }
}
