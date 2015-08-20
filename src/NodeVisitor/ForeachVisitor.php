<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;
use Sstalle\php7cc\Helper\NodeHelper;

class ForeachVisitor extends AbstractVisitor
{
    /**
     * @var \SplStack
     */
    protected $foreachStack;

    /**
     * @var array
     */
    protected $arrayPointerModifyingFunctions = array(
        'current',
        'end',
        'reset',
        'prev',
        'next',
        'each',
    );

    /**
     * @var array
     */
    protected $arrayModifyingFunctions = array(
        'array_pop',
        'array_push',
        'array_shift',
        'array_unshift',
    );

    /**
     */
    public function __construct()
    {
        $this->foreachStack = new \SplStack();
        $this->arrayPointerModifyingFunctions = array_flip($this->arrayPointerModifyingFunctions);
        $this->arrayModifyingFunctions = array_flip($this->arrayModifyingFunctions);
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Foreach_) {
            $this->checkNestedByReferenceForeach($node);
            $this->foreachStack->push($node);
        } elseif (!$this->foreachStack->isEmpty()) {
            $this->checkInternalArrayPointerAccessInByValueForeach($node);
            $this->checkArrayModificationByFunctionInByReferenceForeach($node);
            $this->checkAddingToArrayInByReferenceForeach($node);
        }
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Foreach_) {
            $this->foreachStack->pop();
        }
    }

    /**
     * @param Node $node
     */
    protected function checkInternalArrayPointerAccessInByValueForeach(Node $node)
    {
        if ($this->hasFunctionCallWithForeachArgument($node, $this->arrayPointerModifyingFunctions, true)) {
            $this->addContextMessage(
                'Possible internal array pointer access/modification in a by-value foreach loop',
                $node
            );
        }
    }

    /**
     * @param Node $node
     */
    protected function checkArrayModificationByFunctionInByReferenceForeach(Node $node)
    {
        if ($this->hasFunctionCallWithForeachArgument($node, $this->arrayModifyingFunctions, false)) {
            $this->addContextMessage(
                'Possible array modification using internal function in a by-reference foreach loop',
                $node
            );
        }
    }

    /**
     * @param Node      $node
     * @param array     $functions
     * @param null|bool $skippedByRefType Reference type (by value/by reference) to skip
     *
     * @return bool
     */
    protected function hasFunctionCallWithForeachArgument(Node $node, array $functions, $skippedByRefType = null)
    {
        if (!NodeHelper::isFunctionCallByStaticName($node, $functions)) {
            return false;
        }

        /** @var Node\Expr\FuncCall $node */
        foreach ($node->args as $argument) {
            /** @var Node\Stmt\Foreach_ $foreach */
            foreach ($this->foreachStack as $foreach) {
                if ($skippedByRefType !== null && $foreach->byRef === $skippedByRefType) {
                    continue;
                }

                if ($argument->value instanceof Node\Expr\Variable
                    && $argument->value->name === $this->getForeachVariableName($foreach)
                ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param Node $node
     */
    protected function checkAddingToArrayInByReferenceForeach(Node $node)
    {
        if (!($node instanceof Node\Expr\Assign || $node instanceof Node\Expr\AssignRef)
            || !$node->var instanceof Node\Expr\ArrayDimFetch || !$node->var->var instanceof Node\Expr\Variable
        ) {
            return;
        }

        /** @var Node\Stmt\Foreach_ $foreach */
        foreach ($this->foreachStack as $foreach) {
            if (!$foreach->byRef) {
                continue;
            }

            if ($node->var->var->name === $this->getForeachVariableName($foreach)) {
                $this->addContextMessage(
                    'Possible adding to array on the last iteration of a by-reference foreach loop',
                    $node
                );
            }
        }
    }

    protected function checkNestedByReferenceForeach(Node\Stmt\Foreach_ $foreach)
    {
        if (!$foreach->byRef) {
            return;
        }

        /** @var Node\Stmt\Foreach_ $ancestorForeach */
        foreach ($this->foreachStack as $ancestorForeach) {
            if ($ancestorForeach->byRef) {
                $this->addContextMessage(
                    'Nested by-reference foreach loop, make sure there is no iteration over the same array',
                    $foreach
                );

                return;
            }
        }
    }

    protected function getForeachVariableName(Node\Stmt\Foreach_ $foreach)
    {
        if ($foreach->expr instanceof Node\Expr\Variable) {
            return $foreach->expr->name;
        } elseif (($foreach->expr instanceof Node\Expr\Assign || $foreach->expr instanceof Node\Expr\AssignRef)
            && $foreach->expr->var instanceof Node\Expr\Variable
        ) {
            return $foreach->expr->var->name;
        }

        return;
    }
}
