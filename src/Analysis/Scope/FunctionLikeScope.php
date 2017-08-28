<?php

namespace Sstalle\php7cc\Analysis\Scope;

class FunctionLikeScope
{
    const VARIABLE_VARIABLE_NAME = '*';

    /**
     * @var string[]
     */
    private $parameterNames;

    /**
     * @var array
     */
    private $modifiedVariableNames = array();

    /**
     * @var array Variable names => array of referenced names
     */
    private $referencingVariableNames = array();

    /**
     * @param string[] $parameterNames
     */
    public function __construct(array $parameterNames)
    {
        $this->parameterNames = $parameterNames;
    }

    /**
     * @return string[]
     */
    public function getParameterNames()
    {
        return $this->parameterNames;
    }

    /**
     * @param string $name
     */
    public function addModifiedVariable($name)
    {
        $modifiedNames = array($name);
        if ($name === self::VARIABLE_VARIABLE_NAME) {
            $modifiedNames = $this->parameterNames;
        }

        foreach ($modifiedNames as $modifiedName) {
            $this->markVariableAndReferencesModified($modifiedName);
        }
    }

    /**
     * @param string $name
     */
    public function unsetVariable($name)
    {
        $this->modifiedVariableNames[$name] = true;

        $this->destroyReferences($name);
    }

    /**
     * @param string[] $names
     */
    public function addModifiedVariables(array $names)
    {
        foreach ($names as $name) {
            $this->addModifiedVariable($name);
        }
    }

    /**
     * $toName = &$fromName;.
     *
     * @param string $toName
     * @param string $fromName
     */
    public function createReference($toName, $fromName)
    {
        $this->addModifiedVariable($toName);

        if (!isset($this->referencingVariableNames[$toName])) {
            $this->referencingVariableNames[$toName] = array();
        }

        $this->referencingVariableNames[$toName][] = $fromName;
    }

    /**
     * @param string[] $names
     *
     * @return string[]
     */
    public function filterModifiedVariables(array $names)
    {
        $modifiedVariables = array();
        foreach ($names as $name) {
            if ($this->hasVariableBeenModified($name)) {
                $modifiedVariables[] = $name;
            }
        }

        return array_unique($modifiedVariables);
    }

    /**
     * @param string[] $names
     *
     * @return bool
     */
    public function isAnyVariableModified(array $names)
    {
        return count($this->filterModifiedVariables($names)) > 0;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    private function hasVariableBeenModified($name)
    {
        if (isset($this->modifiedVariableNames[$name])) {
            return true;
        }

        return false;
    }

    /**
     * @param string $name
     */
    private function markVariableAndReferencesModified($name)
    {
        $this->modifiedVariableNames[$name] = true;

        $referencedNames = $this->getVariableNamesReferencedBy($name);
        foreach ($referencedNames as $referencedName) {
            $this->modifiedVariableNames[$referencedName] = true;
        }
    }

    /**
     * @param string   $referencingName
     * @param string[] $processedNames
     *
     * @return string[]
     */
    private function getVariableNamesReferencedBy($referencingName, array $processedNames = array())
    {
        if (!isset($this->referencingVariableNames[$referencingName])) {
            return array();
        }

        $referencedVariableNames = $this->referencingVariableNames[$referencingName];
        if (isset($this->referencingVariableNames[self::VARIABLE_VARIABLE_NAME])) {
            $referencedVariableNames = array_merge(
                $referencedVariableNames,
                $this->referencingVariableNames[self::VARIABLE_VARIABLE_NAME]
            );
        }

        $processingNames = array_merge($processedNames, $referencedVariableNames);
        foreach ($referencedVariableNames as $variableName) {
            $isVariableVariable = $variableName === self::VARIABLE_VARIABLE_NAME;
            $isSelfReference = $variableName === $referencingName;
            $hasBeenProcessed = in_array($variableName, $processedNames, true);
            if ($isSelfReference || $isVariableVariable || $hasBeenProcessed) {
                continue;
            }

            $referencedVariableNames = array_merge(
                $referencedVariableNames,
                $this->getVariableNamesReferencedBy($variableName, $processingNames)
            );
        }

        return array_diff(array_unique($referencedVariableNames), array($referencingName));
    }

    /**
     * @param string $toName
     */
    private function destroyReferences($toName)
    {
        unset($this->referencingVariableNames[$toName]);
        foreach ($this->referencingVariableNames as $name => $referencedNames) {
            $this->referencingVariableNames[$name] = array_diff($referencedNames, array($toName));
        }
    }
}
