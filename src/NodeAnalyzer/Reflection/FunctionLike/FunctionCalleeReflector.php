<?php

namespace Sstalle\php7cc\NodeAnalyzer\Reflection\FunctionLike;

use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use Sstalle\php7cc\Reflection\Reflector\FunctionReflectorInterface;

class FunctionCalleeReflector extends AbstractCalleeReflector
{
    /**
     * @var FunctionReflectorInterface
     */
    private $functionReflector;

    /**
     * @param FunctionReflectorInterface $functionReflector
     */
    public function __construct(FunctionReflectorInterface $functionReflector)
    {
        $this->functionReflector = $functionReflector;
    }

    /**
     * {@inheritdoc}
     */
    public function doGetCalleeReflection($node)
    {
        return $this->functionReflector->reflect($node->name->toString());
    }

    /**
     * {@inheritdoc}
     */
    public function supports($node)
    {
        if (!($node instanceof Expr\FuncCall)) {
            return false;
        }

        return $node->name instanceof Name && $this->functionReflector->supports($node->name->toString());
    }
}
