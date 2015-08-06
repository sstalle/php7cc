<?php

namespace Sstalle\php7cc\Helper\Path;

class UnixPathHelper implements PathHelperInterface
{

    /**
     * @inheritDoc
     */
    public function isAbsolute($path)
    {
        return $path && $path[0] === '/';
    }

    /**
     * @inheritDoc
     */
    public function isDirectoryRelative($path)
    {
        return !$this->isAbsolute($path);
    }

}