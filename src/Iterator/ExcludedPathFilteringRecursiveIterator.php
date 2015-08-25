<?php

namespace Sstalle\php7cc\Iterator;

class ExcludedPathFilteringRecursiveIterator extends AbstractRecursiveFilterIterator
{
    /**
     * @var string[]
     */
    protected $excludedPaths = array();

    /**
     * @param \RecursiveIterator $iterator
     * @param \string[]          $excludedPaths
     */
    public function __construct(\RecursiveIterator $iterator, array $excludedPaths)
    {
        parent::__construct($iterator);
        $this->excludedPaths = array_flip($excludedPaths);
    }

    /**
     * {@inheritdoc}
     */
    public function accept()
    {
        return !isset($this->excludedPaths[realpath($this->key())]);
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren()
    {
        return new static($this->getInnerIterator()->getChildren(), array_flip($this->excludedPaths));
    }
}
