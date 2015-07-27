<?php

namespace Sstalle\php7cc\Iterator;

class ExtensionFilteringRecursiveIterator extends \RecursiveFilterIterator
{

    /**
     * @var array
     */
    protected $allowedExtensions = array();

    /**
     * @param \RecursiveDirectoryIterator $iterator
     * @param string[] $allowedExtensions
     */
    public function __construct(\RecursiveDirectoryIterator $iterator, array $allowedExtensions = array())
    {
        parent::__construct($iterator);
        $this->allowedExtensions = $allowedExtensions;
    }

    /**
     * @inheritDoc
     */
    public function accept()
    {
        $currentKey = $this->key();
        $currentFileExtension = $currentKey ? pathinfo($currentKey, PATHINFO_EXTENSION) : false;

        return !$currentFileExtension || in_array($currentFileExtension, $this->allowedExtensions);
    }

    /**
     * @inheritDoc
     */
    public function getChildren()
    {
        $children = parent::getChildren();
        $children->allowedExtensions = $this->allowedExtensions;

        return $children;
    }

}