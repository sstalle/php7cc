<?php

namespace Sstalle\php7cc;

use Sstalle\php7cc\Helper\Path\PathHelperInterface;

class ExcludedPathCanonicalizer
{

    /**
     * @var PathHelperInterface
     */
    protected $pathHelper;

    /**
     * @param PathHelperInterface $pathHelper
     */
    public function __construct(PathHelperInterface $pathHelper)
    {
        $this->pathHelper = $pathHelper;
    }

    /**
     * Makes all excluded paths absolute. Non-existent paths are removed
     *
     * @param string[] $checkedPaths
     * @param string[] $excludedPaths
     * @return \string[]
     */
    public function canonicalize(array $checkedPaths, array $excludedPaths)
    {
        $checkedDirectories = array_filter($checkedPaths, function($path) {
            return is_dir($path);
        });
        $canonicalizedPaths = array();

        foreach ($excludedPaths as $path) {
            // todo: correct handling of Windows-specific edge cases \foo and z:foo
            if ($this->pathHelper->isAbsolute($path) && ($canonicalizedPath = realpath($path))) {
                $canonicalizedPaths[] = $canonicalizedPath;
            } else {
                foreach ($checkedDirectories as $checkedDirectory) {
                    $nestedExcludedDirectory = realpath(realpath($checkedDirectory) . DIRECTORY_SEPARATOR . $path);
                    $nestedExcludedDirectory && $canonicalizedPaths[] = $nestedExcludedDirectory;
                }
            }
        }

        return $canonicalizedPaths;
    }

}