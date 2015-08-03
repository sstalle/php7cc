<?php

namespace Sstalle\php7cc;

use Sstalle\php7cc\Iterator\ExcludedPathFilteringRecursiveIterator;
use Sstalle\php7cc\Iterator\ExtensionFilteringRecursiveIterator;
use Sstalle\php7cc\Iterator\FileDirectoryListRecursiveIterator;

class PathTraversableFactory
{

    /**
     * @var ExcludedPathCanonicalizer
     */
    protected $excludedPathNormalizer;

    /**
     * @param ExcludedPathCanonicalizer $excludedPathNormalizer
     */
    public function __construct(ExcludedPathCanonicalizer $excludedPathNormalizer)
    {
        $this->excludedPathNormalizer = $excludedPathNormalizer;
    }

    /**
     * @param string[] $paths Files and/or directories to check
     * @param string[] $checkedExtensions Only files having these extensions will be checked
     * @param string[] $excludedPaths
     * @return \Traversable
     */
    public function createTraversable(array $paths, array $checkedExtensions, array $excludedPaths)
    {
        $directlyPassedFiles = array();
        $excludedPaths = $this->excludedPathNormalizer->canonicalize($paths, $excludedPaths);
        foreach ($paths as $path) {
            if (is_dir($path) && !$checkedExtensions) {
                throw new \DomainException('At least 1 extension must be specified to check a directory');
            } elseif (is_file($path)) {
                $directlyPassedFiles[] = realpath($path);
            }
        }

        $fileDirectoryIterator = new FileDirectoryListRecursiveIterator($paths);
        $extensionFilteringIterator = new ExtensionFilteringRecursiveIterator(
            $fileDirectoryIterator,
            $checkedExtensions,
            $directlyPassedFiles
        );
        $excludedPathFilteringIterator = new ExcludedPathFilteringRecursiveIterator(
            $extensionFilteringIterator,
            $excludedPaths
        );

        return new \RecursiveIteratorIterator(
            $excludedPathFilteringIterator,
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
    }

}