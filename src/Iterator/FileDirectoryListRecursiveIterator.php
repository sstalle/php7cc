<?php

namespace Sstalle\php7cc\Iterator;

use Symfony\Component\Finder\Iterator\RecursiveDirectoryIterator;
use Symfony\Component\Finder\SplFileInfo;

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
     * {@inheritdoc}
     */
    public function current()
    {
        $fileName = realpath($this->data[$this->position]);

        return new SplFileInfo($fileName, '', basename($fileName));
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->data[$this->position];
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return isset($this->data[$this->position]);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function hasChildren()
    {
        return is_dir($this->data[$this->position]);
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren()
    {
        return new RecursiveDirectoryIterator(
            $this->data[$this->position],
            \RecursiveDirectoryIterator::KEY_AS_PATHNAME
            | \RecursiveDirectoryIterator::CURRENT_AS_FILEINFO
            | \RecursiveDirectoryIterator::SKIP_DOTS
        );
    }
}
