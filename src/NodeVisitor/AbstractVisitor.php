<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use Sstalle\php7cc\CompatibilityViolation\ContextInterface;
use Sstalle\php7cc\CompatibilityViolation\Message;
use Sstalle\php7cc\Token\TokenCollection;

abstract class AbstractVisitor extends NodeVisitorAbstract implements VisitorInterface
{
    /**
     * @var ContextInterface
     */
    protected $context;

    /**
     * @var TokenCollection
     */
    protected $tokenCollection;

    protected $tokens;

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
     * @param string $text
     * @param Node   $node
     */
    protected function addContextWarning($text, Node $node)
    {
        $this->addContextMessage($text, $node, Message::LEVEL_WARNING);
    }

    /**
     * @param string $text
     * @param Node   $node
     */
    protected function addContextError($text, Node $node)
    {
        $this->addContextMessage($text, $node, Message::LEVEL_ERROR);
    }

    /**
     * @param string $text
     * @param Node   $node
     * @param int    $level
     */
    private function addContextMessage($text, Node $node, $level)
    {
        $this->context->addMessage(new Message($text, $node->getAttribute('startLine'), $level, array($node)));
    }
}
