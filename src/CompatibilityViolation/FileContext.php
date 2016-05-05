<?php

namespace Sstalle\php7cc\CompatibilityViolation;

use Symfony\Component\Finder\SplFileInfo;

class FileContext extends AbstractContext
{
    /**
     * @var SplFileInfo
     */
    protected $file;

    /**
     * @var bool
     */
    protected $useRelativePaths;

    /**
     * @param SplFileInfo $file
     * @param bool        $useRelativePaths
     */
    public function __construct(SplFileInfo $file, $useRelativePaths)
    {
        $this->file = $file;
        $this->useRelativePaths = $useRelativePaths;
    }

    /**
     * @return SplFileInfo
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
        $file = $this->getFile();

        return $this->useRelativePaths ? $file->getRelativePathname() : $file->getRealPath();
    }

    /**
     * {@inheritdoc}
     */
    public function getCheckedCode()
    {
        return $this->getFile()->getContents();
    }
}
