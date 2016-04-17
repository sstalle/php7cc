<?php

namespace Sstalle\php7cc;

use PhpParser\NodeTraverserInterface;
use Sstalle\php7cc\NodeVisitor\ResolverInterface;

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
     * @var NodeTraverserInterface
     */
    protected $traverser;

    /**
     * @var ResolverInterface
     */
    protected $visitorResolver;

    /**
     * @param PathTraversableFactory $pathTraversableFactory
     * @param PathChecker $pathChecker
     * @param NodeTraverserInterface $traverser
     * @param ResolverInterface $visitorResolver
     */
    public function __construct(
        PathTraversableFactory $pathTraversableFactory,
        PathChecker $pathChecker,
        NodeTraverserInterface $traverser,
        ResolverInterface $visitorResolver
    ) {
        $this->pathTraversableFactory = $pathTraversableFactory;
        $this->pathChecker = $pathChecker;
        $this->traverser = $traverser;
        $this->visitorResolver = $visitorResolver;
    }

    /**
     * @param array $paths
     * @param array $checkedExtensions
     * @param array $excludedPaths
     * @param int $messageLevel
     */
    public function check(array $paths, array $checkedExtensions, array $excludedPaths, $messageLevel)
    {
        $this->visitorResolver->setLevel($messageLevel);
        foreach ($this->visitorResolver->resolve() as $visitor) {
            $this->traverser->addVisitor($visitor);
        }

        $this->pathChecker->check(
            $this->pathTraversableFactory->createTraversable(
                $paths,
                $checkedExtensions,
                $excludedPaths
            )
        );
    }
}
