<?php

namespace Sstalle\php7cc\Iterator;

class ExtensionFilteringRecursiveIterator extends \RecursiveFilterIterator
{

    /**
     * @var string[]
     */
    protected $allowedExtensions = array();

    /**
     * @var string[]
     */
    protected $alwaysAllowedFiles = array();

    /**
     * @param \RecursiveIterator $iterator
     * @param string[] $allowedExtensions
     * @param array $alwaysAllowedFiles
     */
    public function __construct(
        \RecursiveIterator $iterator,
        array $allowedExtensions = array(),
        array $alwaysAllowedFiles = array()
    ) {
        parent::__construct($iterator);
        $this->allowedExtensions = $allowedExtensions;
        $this->alwaysAllowedFiles = array_flip($alwaysAllowedFiles);
    }

    /**
     * @inheritDoc
     */
    public function accept()
    {
        $currentKey = $this->key();
        if ($currentKey && isset($this->alwaysAllowedFiles[realpath($currentKey)])) {
            return true;
        }

        $currentFileExtension = $currentKey ? pathinfo($currentKey, PATHINFO_EXTENSION) : false;

        return !$currentFileExtension || in_array($currentFileExtension, $this->allowedExtensions);
    }

    /**
     * @inheritDoc
     */
    public function getChildren()
    {
        return new static($this->getInnerIterator()->getChildren(), $this->allowedExtensions, $this->alwaysAllowedFiles);
    }

}