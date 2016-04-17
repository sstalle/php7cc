<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;
use Sstalle\php7cc\CompatibilityViolation\Message;

class DuplicateFunctionParameterVisitor extends AbstractVisitor
{
    const LEVEL = Message::LEVEL_ERROR;

    /**
     * {@inheritdoc}
     */
    public function enterNode(Node $node)
    {
        if (!$node instanceof Node\FunctionLike) {
            return;
        }

        $parametersNames = array();
        foreach ($node->getParams() as $parameter) {
            $currentParameterName = $parameter->name;
            if (!isset($parametersNames[$currentParameterName])) {
                $parametersNames[$currentParameterName] = false;
            } elseif (!$parametersNames[$currentParameterName]) {
                $this->addContextMessage(
                    sprintf('Duplicate function parameter name "%s"', $currentParameterName),
                    $node
                );

                $parametersNames[$currentParameterName] = true;
            }
        }
    }
}
