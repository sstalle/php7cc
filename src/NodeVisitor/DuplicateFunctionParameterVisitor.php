<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;

class DuplicateFunctionParameterVisitor extends AbstractVisitor
{
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
