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

}