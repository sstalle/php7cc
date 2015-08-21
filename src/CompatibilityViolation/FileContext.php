<?php

namespace Sstalle\php7cc\CompatibilityViolation;

use Sstalle\php7cc\File;

class FileContext extends AbstractContext
{
    /**
     * @var File
     */
    protected $file;

    /**
     * @param File $file
     */
    public function __construct(File $file)
    {
        $this->file = $file;
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * {@inheritdoc}
     */
    public function getCheckedResourceName()
    {
        return $this->getFile()->getRealPath();
    }

    /**
     * {@inheritdoc}
     */
    public function getCheckedCode()
    {
        return $this->getFile()->getContents();
    }
}
