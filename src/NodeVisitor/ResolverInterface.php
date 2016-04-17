<?php

namespace Sstalle\php7cc\NodeVisitor;

interface ResolverInterface
{
    /**
     * @return VisitorInterface[]
     */
    public function resolve();

    /**
     * @param int $level
     */
    public function setLevel($level);
}
