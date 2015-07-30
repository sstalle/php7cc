<?php

namespace Sstalle\php7cc\Iterator;

use RecursiveIterator;

class FileDirectoryListRecursiveIterator implements \RecursiveIterator
{

    /**
     * @var string[]
     */
    protected $data;

    /**
     * @var int
     */
    protected $position = 0;

    /**
     * @param \string[] $data
     */
    public function __construct(array $data)
    {
        foreach ($data as $fileOrDirectory) {
            if (!is_file($fileOrDirectory) && !is_dir($fileOrDirectory)) {
                throw new \UnexpectedValueException(sprintf('%s is not file or directory', $fileOrDirectory));
            }
        }

        $this->data = $data;
    }

    /**
     * @inheritDoc
     */
    public function current()
    {
        return new \SplFileInfo($this->data[$this->position]);
    }

    /**
     * @inheritDoc
     */
    public function next()
    {
        $this->position++;
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        return $this->data[$this->position];
    }

    /**
     * @inheritDoc
     */
    public function valid()
    {
        return isset($this->data[$this->position]);
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * @inheritDoc
     */
    public function hasChildren()
    {
        return is_dir($this->data[$this->position]);
    }

    /**
     * @inheritDoc
     */
    public function getChildren()
    {
        return new \RecursiveDirectoryIterator(
            $this->data[$this->position],
            \RecursiveDirectoryIterator::KEY_AS_PATHNAME
            | \RecursiveDirectoryIterator::CURRENT_AS_FILEINFO
            | \RecursiveDirectoryIterator::SKIP_DOTS
        );
    }

}