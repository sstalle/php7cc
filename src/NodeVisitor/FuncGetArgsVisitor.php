<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;
use Sstalle\php7cc\CompatibilityViolation\Message;
use Sstalle\php7cc\NodeAnalyzer\FunctionAnalyzer;

class FuncGetArgsVisitor extends AbstractVisitor
{
    const LEVEL = Message::LEVEL_WARNING;

    /**
     * @var string[]
     */
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
     * @var FunctionAnalyzer
     */
    protected $functionAnalyzer;

    /**
     * If current function's arguments could have been modified, value on top of the stack is true.
     * Otherwise false.
     *
     * @var \SplStack
     */
    protected $argumentModificationStack;

    /**
     * @param FunctionAnalyzer $functionAnalyzer
     */
    public function __construct(FunctionAnalyzer $functionAnalyzer)
    {
        $this->functionAnalyzer = $functionAnalyzer;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeTraverse(array $nodes)
    {
        $this->argumentModificationStack = new \SplStack();
    }

    /**
     * {@inheritdoc}
     */
    public function enterNode(Node $node)
    {
        $isCurrentNodeFunctionLike = $node instanceof Node\FunctionLike;
        if ($isCurrentNodeFunctionLike || $this->argumentModificationStack->isEmpty()
            || !$this->argumentModificationStack->top()
            || !$this->functionAnalyzer->isFunctionCallByStaticName($node, array_flip(array('func_get_arg', 'func_get_args')))
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

    /**
     * {@inheritdoc}
     */
    public function leaveNode(Node $node)
    {
        if ($this->argumentModificationStack->isEmpty()) {
            return;
        }

        if ($node instanceof Node\FunctionLike) {
            $this->argumentModificationStack->pop();

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
