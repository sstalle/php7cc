<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;

abstract class AbstractNestedLoopVisitor extends AbstractVisitor
{
    /**
     * @var \SplStack
     */
    private $loopStacks;

    /**
     * {@inheritdoc}
     */
    public function beforeTraverse(array $nodes)
    {
        $this->loopStacks = new \SplStack();
        $this->loopStacks->push(new \SplStack());
    }

    /**
     * {@inheritdoc}
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Node\FunctionLike) {
            $this->loopStacks->push(new \SplStack());
        } elseif ($this->isTargetLoopNode($node)) {
            $this->getCurrentLoopStack()->push($node);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\FunctionLike) {
            $this->loopStacks->pop();
        } elseif ($this->isTargetLoopNode($node)) {
            $this->getCurrentLoopStack()->pop();
        }
    }

    /**
     * @return \SplStack
     */
    protected function getCurrentLoopStack()
    {
        return $this->loopStacks->top();
    }

    /**
     * @param Node $node
     *
     * @return bool
     */
    abstract protected function isTargetLoopNode(Node $node);
}
