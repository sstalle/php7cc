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
     * @param PathChecker            $pathChecker
     * @param NodeTraverserInterface $traverser
     * @param ResolverInterface      $visitorResolver
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
     * @param PathCheckSettings $checkSettings
     */
    public function check(PathCheckSettings $checkSettings)
    {
        $this->visitorResolver->setLevel($checkSettings->getMessageLevel());
        foreach ($this->visitorResolver->resolve() as $visitor) {
            $this->traverser->addVisitor($visitor);
        }

        $this->pathChecker->check(
            $this->pathTraversableFactory->createTraversable(
                $checkSettings->getCheckedPaths(),
                $checkSettings->getCheckedFileExtensions(),
                $checkSettings->getExcludedPaths()
            ),
            $checkSettings->getUseRelativePaths()
        );
    }
}
