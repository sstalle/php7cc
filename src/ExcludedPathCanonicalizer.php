<?php

namespace Sstalle\php7cc;

class ExcludedPathCanonicalizer
{

    /**
     * Makes all excluded paths absolute
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
            if (is_file($path) || is_dir($path)) {
                $canonicalizedPaths[] = realpath($path);
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