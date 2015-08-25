<?php

namespace Sstalle\php7cc\Iterator;

class ExtensionFilteringRecursiveIterator extends AbstractRecursiveFilterIterator
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
     * @param string[]           $allowedExtensions
     * @param array              $alwaysAllowedFiles
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
     * {@inheritdoc}
     */
    public function accept()
    {
        $currentKey = $this->key();
        $isFile = $currentKey && is_file($currentKey);

        if ($isFile && isset($this->alwaysAllowedFiles[realpath($currentKey)])) {
            return true;
        }

        return !$isFile || in_array(pathinfo($currentKey, PATHINFO_EXTENSION), $this->allowedExtensions);
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren()
    {
        return new static(
            $this->getInnerIterator()->getChildren(),
            $this->allowedExtensions,
            array_flip($this->alwaysAllowedFiles)
        );
    }
}
