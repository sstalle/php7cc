<?php

namespace Sstalle\php7cc\NodeAnalyzer\Reflection\FunctionLike;

use PhpParser\Node\Expr;
use Sstalle\php7cc\Reflection\Reflector\ReflectorInterface;

class FunctionLikeCalleeReflector extends AbstractCalleeReflector
{
    /**
     * @var ReflectorInterface[]
     */
    protected $delegateReflectors;

    /**
     * @param ReflectorInterface[] $delegateReflectors
     */
    public function __construct(array $delegateReflectors)
    {
        $this->delegateReflectors = $delegateReflectors;
    }

    /**
     * {@inheritdoc}
     */
    public function doGetCalleeReflection($node)
    {
        return $this->getSupportedReflector($node)->reflect($node);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($node)
    {
        return $this->getSupportedReflector($node) !== null;
    }

    /**
     * @param Expr\StaticCall|Expr\FuncCall|Expr\MethodCall $node
     *
     * @return CalleeReflectorInterface|null
     */
    private function getSupportedReflector($node)
    {
        foreach ($this->delegateReflectors as $reflector) {
            if ($reflector->supports($node)) {
                return $reflector;
            }
        }

        return;
    }
}
