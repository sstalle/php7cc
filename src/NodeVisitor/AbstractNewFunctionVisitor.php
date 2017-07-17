<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;
use Sstalle\php7cc\NodeAnalyzer\FunctionAnalyzer;

abstract class AbstractNewFunctionVisitor extends AbstractVisitor
{
    const FUNCTION_EXISTS_FUNCTION_NAME = 'function_exists';

    /**
     * @var string[]
     */
    private static $newFunctions = array(
        'random_bytes',
        'random_int',

        'error_clear_last',

        'gmp_random_seed',

        'intdiv',

        'preg_replace_callback_array',

        'posix_setrlimit',

        'inflate_add',
        'inflate_init',
        'deflate_add',
        'deflate_init',
    );

    /**
     * @var string[]
     */
    private static $lowerCasedNewFunctions = array();

    /**
     * @var \SplStack
     */
    private $ifStatementStack;

    /**
     * @var FunctionAnalyzer
     */
    private $functionAnalyzer;

    /**
     * @param FunctionAnalyzer $functionAnalyzer
     */
    public function __construct(FunctionAnalyzer $functionAnalyzer)
    {
        foreach (self::$newFunctions as $function) {
            self::$lowerCasedNewFunctions[strtolower($function)] = $function;
        }

        $this->ifStatementStack = new \SplStack();
        $this->functionAnalyzer = $functionAnalyzer;
    }

    /**
     * {@inheritdoc}
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\If_) {
            $this->ifStatementStack->push($node);

            return;
        }

        if ($this->isNewFunctionCall($node)
            && !$this->isFunctionExistenceChecked($node)
            && $this->accepts($node)
        ) {
            $declaredFunctionName = $this->extractNormalizedFunctionName($node);
            $this->addContextMessage($this->getMessageText(self::$lowerCasedNewFunctions[$declaredFunctionName]), $node);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function leaveNode(Node $node)
    {
        if (!$this->ifStatementStack->isEmpty() && $this->ifStatementStack->top() === $node) {
            $this->ifStatementStack->pop();
        }
    }

    /**
     * @param Node\Stmt\Function_ $node
     *
     * @return bool
     */
    abstract protected function accepts(Node\Stmt\Function_ $node);

    /**
     * @param string $functionName
     *
     * @return string
     */
    abstract protected function getMessageText($functionName);

    /**
     * @param Node $node
     *
     * @return bool
     */
    private function isNewFunctionCall(Node $node)
    {
        return $node instanceof Node\Stmt\Function_
            && ($lowerCasedFunction = $this->extractNormalizedFunctionName($node))
            && array_key_exists($lowerCasedFunction, self::$lowerCasedNewFunctions);
    }

    /**
     * @param Node\Stmt\Function_ $declaredFunction
     *
     * @return bool
     */
    private function isFunctionExistenceChecked(Node\Stmt\Function_ $declaredFunction)
    {
        /** @var Node\Stmt\If_ $ifStatement */
        foreach ($this->ifStatementStack as $ifStatement) {
            $condition = $ifStatement->cond;

            $isConditionNegatedFunctionExistsCall = $condition
                && ($condition instanceof Node\Expr\BooleanNot)
                && $this->functionAnalyzer->isFunctionCallByStaticName($condition->expr, static::FUNCTION_EXISTS_FUNCTION_NAME);
            if (!$isConditionNegatedFunctionExistsCall) {
                continue;
            }

            /** @var Node\Expr\FuncCall $conditionFunction */
            $conditionFunction = $condition->expr;
            $checkedFunctionName = isset($conditionFunction->args[0]) ? $conditionFunction->args[0] : null;
            $isCheckedFunctionNameScalarString = $checkedFunctionName
                && ($checkedFunctionName instanceof Node\Arg)
                && ($checkedFunctionName->value instanceof Node\Scalar\String_);
            if (!$isCheckedFunctionNameScalarString) {
                continue;
            }

            $checkedFunctionName = $this->normalizeFunctionName($checkedFunctionName->value->value);
            if ($checkedFunctionName && $checkedFunctionName[0] === '\\') {
                $checkedFunctionName = substr($checkedFunctionName, 1);
            }

            $declaredFunctionName = $this->extractNormalizedFunctionName($declaredFunction);
            if ($checkedFunctionName === $declaredFunctionName) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Node\Stmt\Function_ $function
     *
     * @return string
     */
    private function extractNormalizedFunctionName(Node\Stmt\Function_ $function)
    {
        return $this->normalizeFunctionName($function->name);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private function normalizeFunctionName($name)
    {
        return strtolower($name);
    }
}
