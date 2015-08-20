<?php

namespace Sstalle\php7cc\Helper\Path;

class UnixPathHelper implements PathHelperInterface
{
    /**
     * {@inheritdoc}
     */
    public function isAbsolute($path)
    {
        return $path && $path[0] === '/';
    }

    /**
     * {@inheritdoc}
     */
    public function isDirectoryRelative($path)
    {
        return !$this->isAbsolute($path);
    }
}
