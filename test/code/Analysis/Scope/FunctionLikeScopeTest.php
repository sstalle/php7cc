<?php

namespace code\Analysis\Scope;

use Sstalle\php7cc\Analysis\Scope\FunctionLikeScope;

class FunctionLikeScopeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider returnsCorrectParameterNamesProvider
     */
    public function testReturnsCorrectParameterNames(array $parameterNames)
    {
        $scope = new FunctionLikeScope($parameterNames);

        $this->assertSame($parameterNames, $scope->getParameterNames());
    }

    /**
     * @dataProvider addingModifiedVariableMarksItAsModifiedProvider
     */
    public function testAddingModifiedVariableMarksItAsModified(array $parameterNames, array $modifiedVariableNames)
    {
        $scope = new FunctionLikeScope($parameterNames);
        foreach ($modifiedVariableNames as $name) {
            $scope->addModifiedVariable($name);
        }

        $allVariableNames = array_merge($parameterNames, $modifiedVariableNames);
        $actualModifiedVariableNames = $scope->filterModifiedVariables($allVariableNames);
        $this->assertSame(sort($modifiedVariableNames), sort($actualModifiedVariableNames));
        $this->assertSame(!empty($modifiedVariableNames), $scope->isAnyVariableModified($modifiedVariableNames));
    }

    /**
     * @dataProvider variablesAreMarkedAsModifiedByReferenceProvider
     */
    public function testVariablesAreMarkedAsModifiedByReference(
        array $parameterNames,
        array $references,
        array $modifiedVariableNames,
        array $expectedModifiedVariableNames
    ) {
        $scope = new FunctionLikeScope($parameterNames);
        foreach ($references as $toName => $fromName) {
            $scope->createReference($toName, $fromName);
        }

        foreach ($modifiedVariableNames as $name) {
            $scope->addModifiedVariable($name);
        }

        $allVariableNames = array_merge($parameterNames, array_keys($references), array_values($references));
        $actualModifiedVariableNames = $scope->filterModifiedVariables($allVariableNames);
        $this->assertSame(sort($expectedModifiedVariableNames), sort($actualModifiedVariableNames));
    }

    /**
     * @dataProvider unsettingAVariableModifiedItProvider
     */
    public function testUnsettingAVariableModifiedIt(array $parameterNames, $variableNamesToUnset)
    {
        $scope = new FunctionLikeScope($parameterNames);
        foreach ($variableNamesToUnset as $name) {
            $scope->unsetVariable($name);
        }

        $allVariableNames = array_merge($parameterNames, $variableNamesToUnset);
        $actualModifiedVariables = $scope->filterModifiedVariables($allVariableNames);
        $this->assertSame(sort($variableNamesToUnset), sort($actualModifiedVariables));
    }

    /**
     * @dataProvider unsettingAVariableDestroysReferenceProvider
     */
    public function testUnsettingAVariableDestroysReference(
        array $parameterNames,
        array $references,
        array $unsetVariablesNames,
        array $modifiedVariableNames,
        array $expectedModifiedVariableNames
    ) {
        $scope = new FunctionLikeScope($parameterNames);
        foreach ($references as $toName => $fromName) {
            $scope->createReference($toName, $fromName);
        }

        foreach ($unsetVariablesNames as $name) {
            $scope->unsetVariable($name);
        }

        foreach ($modifiedVariableNames as $name) {
            $scope->addModifiedVariable($name);
        }

        $allVariableNames = array_merge($parameterNames, array_keys($references), array_values($references));
        $actualModifiedVariableNames = $scope->filterModifiedVariables($allVariableNames);
        $this->assertSame(sort($expectedModifiedVariableNames), sort($actualModifiedVariableNames));
    }

    public function returnsCorrectParameterNamesProvider()
    {
        return array(
            array(array()),
            array(array('foo')),
            array(array('foo', 'bar')),
        );
    }

    public function addingModifiedVariableMarksItAsModifiedProvider()
    {
        return array(
            array(
                array(),
                array('mod1'),
            ),
            array(
                array('foo'),
                array('mod1', 'mod2'),
            ),
            array(
                array('foo', 'bar'),
                array('mod1'),
            ),
            array(
                array('foo', 'bar'),
                array('modbar'),
            ),
        );
    }

    public function variablesAreMarkedAsModifiedByReferenceProvider()
    {
        return array(
            array(
                array('param1', 'param2'),
                array(),
                array(),
                array(),
            ),
            array(
                array('param1', 'param2'),
                array('foo' => 'param1'),
                array('foo'),
                array('foo', 'param1'),
            ),
            array(
                array('param1', 'param2'),
                array('foo' => 'param1'),
                array(),
                array('foo'),
            ),
            array(
                array(),
                array('foo' => 'bar'),
                array('foo'),
                array('foo', 'bar'),
            ),
        );
    }

    public function unsettingAVariableModifiedItProvider()
    {
        return array(
            array(
                array(),
                array(),
            ),
            array(
                array('foo', 'bar'),
                array('mod1'),
            ),
            array(
                array('foo', 'bar'),
                array('foo'),
            ),
        );
    }

    public function unsettingAVariableDestroysReferenceProvider()
    {
        return array(
            array(
                array('param1', 'param2'),
                array(),
                array(),
                array(),
                array(),
            ),
            array(
                array('param1', 'param2'),
                array('foo' => 'param1'),
                array('foo'),
                array('foo'),
                array('foo'),
            ),
            array(
                array('param1', 'param2'),
                array('foo' => 'param1', 'bar' => 'param2'),
                array('foo'),
                array('foo', 'bar'),
                array('foo', 'bar', 'param2'),
            ),
        );
    }
}
