<?php

namespace Sstalle\php7cc\NodeAnalyzer;

use PhpParser\Node;
use Sstalle\php7cc\NodeAnalyzer\Reflection\FunctionLike\CalleeReflectorInterface;

class FunctionAnalyzer
{
    /**
     * @var CalleeReflectorInterface
     */
    private $calleeReflector;

    /**
     * @param CalleeReflectorInterface $functionReflector
     */
    public function __construct(CalleeReflectorInterface $functionReflector)
    {
        $this->calleeReflector = $functionReflector;
    }

    /**
     * @param Node            $node
     * @param string|string[] $checkedFunctionName If an array as passed, function names should be keys
     *
     * @return bool
     */
    public function isFunctionCallByStaticName(Node $node, $checkedFunctionName)
    {
        $isFunctionCallByStaticName = $node instanceof Node\Expr\FuncCall && $node->name instanceof Node\Name;
        if (!$isFunctionCallByStaticName) {
            return $isFunctionCallByStaticName;
        }

        $calledFunctionName = strtolower($node->name->toString());

        return is_array($checkedFunctionName)
            ? isset($checkedFunctionName[$calledFunctionName])
            : $calledFunctionName === $checkedFunctionName;
    }

    /**
     * Returns arguments passed by reference, if reflection for the called function
     * is available. Otherwise returns all the arguments.
     *
     * @param Node\Expr\FuncCall $callNode
     *
     * @return array|Node\Arg[]
     */
    public function getByReferenceCallArguments($callNode)
    {
        $byReferenceArguments = $callNode->args;
        if (!$this->calleeReflector->supports($callNode)) {
            return $byReferenceArguments;
        }

        $reflection = $this->calleeReflector->reflect($callNode);
        $byReferenceParameterPositions = array_flip($reflection->getByReferenceParameterPositions());
        $byReferenceArguments = array_intersect_key($byReferenceArguments, $byReferenceParameterPositions);

        return $byReferenceArguments;
    }
}
