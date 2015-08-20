<?php

namespace Sstalle\php7cc\Helper\Path;

interface PathHelperInterface
{
    /**
     * @param string $path
     *
     * @return bool
     */
    public function isAbsolute($path);

    /**
     * @param string $path
     *
     * @return bool
     */
    public function isDirectoryRelative($path);
}
