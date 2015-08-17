<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;
use Sstalle\php7cc\Helper\NodeHelper;

class FuncGetArgsVisitor extends AbstractVisitor
{

    protected $possiblyArgumentModifyingClasses = array(
        'PhpParser\\Node\\Stmt\\Foreach_',
        'PhpParser\\Node\\Stmt\\Global_',
        'PhpParser\\Node\\Stmt\\Unset_',
        'PhpParser\\Node\\Expr\\Assign',
        'PhpParser\\Node\\Expr\\AssignOp',
        'PhpParser\\Node\\Expr\\AssignRef',
        'PhpParser\\Node\\Expr\\FuncCall',
        'PhpParser\\Node\\Expr\\List',
        'PhpParser\\Node\\Expr\\MethodCall',
        'PhpParser\\Node\\Expr\\PostDec',
        'PhpParser\\Node\\Expr\\PostInc',
        'PhpParser\\Node\\Expr\\PreDec',
        'PhpParser\\Node\\Expr\\PreInc',
        'PhpParser\\Node\\Expr\\StaticCall',
    );

    /**
     * If current function's arguments could have been modified, value on top of the stack is true.
     * Otherwise false.
     *
     * @var \SplStack
     */
    protected $argumentModificationStack;

    /**
     */
    public function __construct()
    {
        $this->argumentModificationStack = new \SplStack();
    }

    public function enterNode(Node $node)
    {
        $isCurrentNodeFunctionLike = $node instanceof Node\FunctionLike;
        if ($isCurrentNodeFunctionLike || $this->argumentModificationStack->isEmpty()
            || !$this->argumentModificationStack->top()
            || !NodeHelper::isFunctionCallByStaticName($node, array_flip(array('func_get_arg', 'func_get_args')))
        ) {
            $isCurrentNodeFunctionLike && $this->argumentModificationStack->push(false);

            return;
        }

        /** @var Node\Expr\FuncCall $node */
        $functionName = $node->name->toString();
        $this->addContextMessage(
            sprintf('Function argument(s) returned by "%s" might have been modified', $functionName),
            $node
        );
    }

    public function leaveNode(Node $node)
    {
        if ($this->argumentModificationStack->isEmpty()) {
            return;
        }

        foreach ($this->possiblyArgumentModifyingClasses as $class) {
            if ($node instanceof $class) {
                $this->argumentModificationStack->pop();
                $this->argumentModificationStack->push(true);

                return;
            }
        }
    }

}