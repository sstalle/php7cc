<?php

namespace Sstalle\php7cc\NodeAnalyzer\Reflection\FunctionLike;

use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use Sstalle\php7cc\Reflection\Reflector\ClassReflectorInterface;

class MethodCalleeReflector extends AbstractCalleeReflector
{
    /**
     * @var ClassReflectorInterface
     */
    private $classReflector;

    /**
     * @param ClassReflectorInterface $classReflector
     */
    public function __construct(ClassReflectorInterface $classReflector)
    {
        $this->classReflector = $classReflector;
    }

    /**
     * {@inheritdoc}
     */
    public function doGetCalleeReflection($node)
    {
        $reflectionClass = $this->classReflector->reflect($node->class->toString());

        return $reflectionClass->getMethod($node->name);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($node)
    {
        if (!($node instanceof Expr\StaticCall)) {
            return false;
        }

        if (!is_string($node->name) || !($node->class instanceof Name)) {
            return false;
        }

        return $this->classReflector->supports($node->class->toString());
    }
}
