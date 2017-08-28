<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;
use Sstalle\php7cc\CompatibilityViolation\Message;
use Sstalle\php7cc\NodeAnalyzer\FunctionAnalyzer;

class FuncGetArgsVisitor extends AbstractVisitor
{
    const LEVEL = Message::LEVEL_WARNING;

    const ANY_VARIABLE_NAME = '*';

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
     * @var \SplStack|Node\string[][]
     */
    protected $functionParameterNames;

    /**
     * @var \SplStack
     *
     * A stack of hashes [$referencingVariableName => [$referencedVariableName1, ...]]
     */
    protected $referencingVariableNames;

    /**
     * A stack of hashes of variable names that could have been modified during
     * function execution.
     *
     * @var \SplStack
     */
    protected $modifiedVariableNames;

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
        $this->modifiedVariableNames = new \SplStack();
        $this->functionParameterNames = new \SplStack();
        $this->referencingVariableNames = new \SplStack();
    }

    /**
     * {@inheritdoc}
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Node\FunctionLike) {
            $this->functionParameterNames->push($this->extractParametersNames($node->getParams()));
            $this->modifiedVariableNames->push(array());
            $this->referencingVariableNames->push(array());

            return;
        }

        $modifiedVariablesExist = !$this->modifiedVariableNames->isEmpty() && $this->modifiedVariableNames->top();
        if (!$modifiedVariablesExist
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
        if ($this->functionParameterNames->isEmpty()) {
            return;
        }

        if ($node instanceof Node\FunctionLike) {
            $this->functionParameterNames->pop();
            $this->modifiedVariableNames->pop();
            $this->referencingVariableNames->pop();

            return;
        }

        foreach ($this->argumentModifyingClasses as $class) {
            if ($node instanceof $class) {
                $previouslyModifiedVariableNames = $this->modifiedVariableNames->pop();
                foreach ($this->getModifiedVariableNames($node) as $name) {
                    $previouslyModifiedVariableNames[$name] = true;
                }
                $this->modifiedVariableNames->push($previouslyModifiedVariableNames);

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

            case $node instanceof Node\Stmt\Global_:
            case $node instanceof Node\Stmt\Unset_:
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
        if ($this->functionParameterNames->isEmpty()) {
            return array();
        }

        $allParameterNames = $this->functionParameterNames->top();
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
        /**
         * If the reference is assigned to an object property, the warning is not relevant,
         * because the user probably expects the assigned property to be modified.
         */
        $isAssignedToVariable = $node->var instanceof Node\Expr\Variable;
        if ($this->referencingVariableNames->isEmpty()
            || !$isAssignedToVariable
        ) {
            return array();
        }

        $referencedVariableName = $this->convertVariableNameToString($node->expr);
        $referencingVariableName = $this->convertVariableNameToString($node->var);

        $currentReferencingVariableNames = $this->referencingVariableNames->pop();
        if (!isset($currentReferencingVariableNames[$referencingVariableName])) {
            $currentReferencingVariableNames[$referencingVariableName] = array();
        }

        $currentReferencingVariableNames[$referencingVariableName][] = $referencedVariableName;
        $this->referencingVariableNames->push($currentReferencingVariableNames);

        return $referencingVariableName === self::ANY_VARIABLE_NAME
            ? $this->functionParameterNames->top()
            : array($referencingVariableName);
    }

    /**
     * @param string[] $checkedParameterNames
     *
     * @return bool
     */
    private function currentFunctionHasModifiedArguments(array $checkedParameterNames)
    {
        if ($this->modifiedVariableNames->isEmpty()) {
            return false;
        }

        $modifiedVariableNames = array_keys($this->modifiedVariableNames->top());

        return count(array_intersect($checkedParameterNames, $modifiedVariableNames)) > 0;
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
            if ($variableName === self::ANY_VARIABLE_NAME) {
                $modifiedNames = $this->functionParameterNames->top();
                break;
            }

            $modifiedNames[] = $variableName;
        }

        foreach ($modifiedNames as $name) {
            $modifiedNames = array_merge($modifiedNames, $this->getVariableNamesReferencedBy($name));
        }

        return array_unique($modifiedNames);
    }

    /**
     * @param string $referencingVariableName
     *
     * @return string[]
     */
    private function getVariableNamesReferencedBy($referencingVariableName)
    {
        if ($this->referencingVariableNames->isEmpty()) {
            return array();
        }

        $allReferencedVariables = $this->referencingVariableNames->top();
        $variableNamesReferencedByArgument = isset($allReferencedVariables[$referencingVariableName])
            ? $allReferencedVariables[$referencingVariableName]
            : array()
        ;
        if (isset($allReferencedVariables[self::ANY_VARIABLE_NAME])) {
            $variableNamesReferencedByArgument = array_merge(
                $variableNamesReferencedByArgument,
                $allReferencedVariables[self::ANY_VARIABLE_NAME]
            );
        }

        $referencesAnyVariable = false;
        foreach ($variableNamesReferencedByArgument as $variableName) {
            if ($variableName === $referencingVariableName) {
                continue;
            }

            if ($variableName === self::ANY_VARIABLE_NAME) {
                $referencesAnyVariable = true;

                continue;
            }

            $variableNamesReferencedByArgument = array_merge(
                $variableNamesReferencedByArgument,
                $this->getVariableNamesReferencedBy($variableName)
            );
        }

        if ($referencesAnyVariable) {
            $variableNamesReferencedByArgument = array_merge(
                $variableNamesReferencedByArgument,
                $this->functionParameterNames->top()
            );
        }

        return $variableNamesReferencedByArgument;
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
            : self::ANY_VARIABLE_NAME;
    }
}
