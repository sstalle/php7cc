<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;
use Sstalle\php7cc\Analysis\Scope\FunctionLikeScope;
use Sstalle\php7cc\CompatibilityViolation\Message;
use Sstalle\php7cc\NodeAnalyzer\FunctionAnalyzer;

class FuncGetArgsVisitor extends AbstractVisitor
{
    const LEVEL = Message::LEVEL_WARNING;

    /**
     * @var string[]
     */
    protected $argumentModifyingClasses = array(
        'PhpParser\\Node\\Stmt\\Foreach_',
        'PhpParser\\Node\\Stmt\\Global_',
        'PhpParser\\Node\\Stmt\\Unset_',
        'PhpParser\\Node\\Expr\\Assign',
        'PhpParser\\Node\\Expr\\AssignOp',
        'PhpParser\\Node\\Expr\\AssignRef',
        'PhpParser\\Node\\Expr\\FuncCall',
        'PhpParser\\Node\\Expr\\List_',
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
     * @var FunctionLikeScope[]|\SplStack
     */
    protected $scopes;

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
        $this->scopes = new \SplStack();
    }

    /**
     * {@inheritdoc}
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Node\FunctionLike) {
            $parameterNames = $this->extractParametersNames($node->getParams());
            $this->scopes->push(new FunctionLikeScope($parameterNames));

            return;
        }

        $scope = $this->getCurrentScope();
        if (!$scope
            || !$this->functionAnalyzer->isFunctionCallByStaticName($node, array_flip(array('func_get_arg', 'func_get_args')))
        ) {
            return;
        }

        $parameterNames = $this->getParameterNamesReturnedBy($node);
        if ($this->currentFunctionHasModifiedArguments($parameterNames)) {
            /** @var Node\Expr\FuncCall $node */
            $functionName = $node->name->toString();
            $this->addContextMessage(
                sprintf('Function argument(s) returned by "%s" might have been modified', $functionName),
                $node
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function leaveNode(Node $node)
    {
        $scope = $this->getCurrentScope();
        if (!$scope) {
            return;
        }

        if ($node instanceof Node\FunctionLike) {
            $this->scopes->pop();

            return;
        }

        foreach ($this->argumentModifyingClasses as $class) {
            if ($node instanceof $class) {
                $modifiedVariableNames = $this->getModifiedVariableNames($node);
                $scope->addModifiedVariables($modifiedVariableNames);

                return;
            }
        }
    }

    /**
     * @param Node $node
     *
     * @return string[]
     */
    private function getModifiedVariableNames(Node $node)
    {
        switch (true) {
            case $node instanceof Node\Stmt\Foreach_:
                return $this->extractModifiedVariableNames(array($node->keyVar, $node->valueVar));

            case $node instanceof Node\Stmt\Unset_:
                $modifiedNames = $this->extractModifiedVariableNames($node->vars);
                $scope = $this->getCurrentScope();
                foreach ($modifiedNames as $name) {
                    $scope->unsetVariable($name);
                }

                return $modifiedNames;

            case $node instanceof Node\Stmt\Global_:
            case $node instanceof Node\Expr\List_:
                return $this->extractModifiedVariableNames($node->vars);

            case $node instanceof Node\Expr\Assign:
            case $node instanceof Node\Expr\AssignOp:
            case $node instanceof Node\Expr\PostDec:
            case $node instanceof Node\Expr\PostInc:
            case $node instanceof Node\Expr\PreDec:
            case $node instanceof Node\Expr\PreInc:
                return $this->extractModifiedVariableNames(array($node->var));

            case $node instanceof Node\Expr\FuncCall:
            case $node instanceof Node\Expr\StaticCall:
            case $node instanceof Node\Expr\MethodCall:
                $byReferenceArguments = $this->functionAnalyzer->getByReferenceCallArguments($node);

                return $this->extractModifiedVariableNames($byReferenceArguments);

            case $node instanceof Node\Expr\AssignRef:
                return $this->addVariableReference($node);

            default:
                throw new \InvalidArgumentException(sprintf('Unknown node type %s', get_class($node)));
        }
    }

    /**
     * Returns current function's parameter names that could be returned
     * from a func_get_args/func_get_arg call.
     *
     * @param Node\Expr\FuncCall $getArgsCall
     *
     * @return string[]
     */
    private function getParameterNamesReturnedBy(Node\Expr\FuncCall $getArgsCall)
    {
        $scope = $this->getCurrentScope();
        if (!$scope) {
            return array();
        }

        $allParameterNames = $scope->getParameterNames();
        $argumentNumber = isset($getArgsCall->args[0]) ? $getArgsCall->args[0] : null;
        if ($argumentNumber
            && $this->functionAnalyzer->isFunctionCallByStaticName($getArgsCall, 'func_get_arg')
            && $argumentNumber->value
            && $argumentNumber->value instanceof Node\Scalar\LNumber
        ) {
            $argumentNumber = $argumentNumber->value->value;

            return isset($allParameterNames[$argumentNumber])
                ? array($allParameterNames[$argumentNumber])
                : array();
        }

        return $allParameterNames;
    }

    /**
     * @param Node\Expr\AssignRef $node
     *
     * @return string[]
     */
    private function addVariableReference(Node\Expr\AssignRef $node)
    {
        $scope = $this->getCurrentScope();

        /**
         * If the reference is assigned to an object property, the warning is not relevant,
         * because the user probably expects the assigned property to be modified.
         */
        $isAssignedToVariable = $node->var instanceof Node\Expr\Variable;
        if (!$scope || !$isAssignedToVariable) {
            return array();
        }

        $fromVariableName = $this->convertVariableNameToString($node->expr);
        $toVariableName = $this->convertVariableNameToString($node->var);

        $scope = $this->getCurrentScope();
        $scope->createReference($toVariableName, $fromVariableName);

        return array();
    }

    /**
     * @param string[] $checkedParameterNames
     *
     * @return bool
     */
    private function currentFunctionHasModifiedArguments(array $checkedParameterNames)
    {
        $scope = $this->getCurrentScope();

        return $scope && $scope->isAnyVariableModified($checkedParameterNames);
    }

    /**
     * @param Node\Param[] $parameters
     *
     * @return string[]
     */
    private function extractParametersNames(array $parameters)
    {
        return array_map(function (Node\Param $parameter) {
            return $parameter->name;
        }, $parameters);
    }

    /**
     * @param Node[] $nodes
     *
     * @return string[]
     */
    private function extractModifiedVariableNames(array $nodes)
    {
        $modifiedNames = array();
        foreach ($nodes as $node) {
            if (!$node) {
                continue;
            }

            $variableNameNode = $node;
            if ($node instanceof Node\Arg) {
                $variableNameNode = $node->value;
            }

            if ($node instanceof Node\Expr\ArrayDimFetch) {
                $variableNameNode = $node->var;
            }

            if (!($variableNameNode instanceof Node\Expr\Variable)) {
                continue;
            }

            $variableName = $this->convertVariableNameToString($variableNameNode);
            $modifiedNames[] = $variableName;
        }

        return array_unique($modifiedNames);
    }

    /**
     * @param Node $variableNode
     *
     * @return string
     */
    private function convertVariableNameToString(Node $variableNode)
    {
        return $variableNode instanceof Node\Expr\Variable && is_string($variableNode->name)
            ? $variableNode->name
            : FunctionLikeScope::VARIABLE_VARIABLE_NAME;
    }

    /**
     * @return FunctionLikeScope|null
     */
    private function getCurrentScope()
    {
        return $this->scopes->isEmpty() ? null : $this->scopes->top();
    }
}
