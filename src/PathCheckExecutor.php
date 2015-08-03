<?php

namespace Sstalle\php7cc;

class PathCheckExecutor
{

    /**
     * @var PathTraversableFactory
     */
    protected $pathTraversableFactory;

    /**
     * @var PathChecker
     */
    protected $pathChecker;

    /**
     * @param PathTraversableFactory $pathTraversableFactory
     * @param PathChecker $pathChecker
     */
    public function __construct(PathTraversableFactory $pathTraversableFactory, PathChecker $pathChecker)
    {
        $this->pathTraversableFactory = $pathTraversableFactory;
        $this->pathChecker = $pathChecker;
    }

    public function check(array $paths, array $checkedExtensions, array $excludedPaths)
    {
        $this->pathChecker->check(
            $this->pathTraversableFactory->createTraversable(
                $paths,
                $checkedExtensions,
                $excludedPaths
            )
        );
    }

}