<?php

namespace Sstalle\php7cc\NodeTraverser;

use PhpParser\NodeTraverser;
use Sstalle\php7cc\CompatibilityViolation\ContextInterface;
use Sstalle\php7cc\NodeVisitor\VisitorInterface;
use Sstalle\php7cc\Token\TokenCollection;

class Traverser extends NodeTraverser
{
    /**
     * {@inheritdoc}
     */
    public function traverse(array $nodes, ContextInterface $context = null, array $tokens = array())
    {
        if ($context) {
            foreach ($this->visitors as $visitor) {
                if ($visitor instanceof VisitorInterface) {
                    $visitor->initializeContext($context);
                    $visitor->setTokenCollection(new TokenCollection($tokens));
                }
            }
        }

        return parent::traverse($nodes);
    }
}
