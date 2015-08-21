<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use Sstalle\php7cc\CompatibilityViolation\ContextInterface;
use Sstalle\php7cc\CompatibilityViolation\Message;

abstract class AbstractVisitor extends NodeVisitorAbstract implements VisitorInterface
{
    /**
     * @var ContextInterface
     */
    protected $context;

    /**
     * @var array
     */
    protected $tokens = array();

    public function initializeContext(ContextInterface $context)
    {
        $this->context = $context;
    }

    /**
     * @param array $tokens
     */
    public function setTokens(array $tokens)
    {
        $this->tokens = $tokens;
    }

    /**
     * @param string $text
     * @param Node   $node
     */
    protected function addContextMessage($text, Node $node)
    {
        $this->context->addMessage(new Message($text, $node->getAttribute('startLine'), array($node)));
    }
}
