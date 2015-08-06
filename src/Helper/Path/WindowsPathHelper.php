<?php

namespace Sstalle\php7cc\Helper\Path;

class WindowsPathHelper implements PathHelperInterface
{

    /**
     * @inheritDoc
     */
    public function isAbsolute($path)
    {
        return $path && (preg_match('#^(\\\\\\\\|[a-zA-Z]\\:\\\\|\\\\.+)#', $path) === 1);
    }

    /**
     * @inheritDoc
     */
    public function isDirectoryRelative($path)
    {
        return $path && (!$this->isAbsolute($path) && preg_match('#^[a-zA-Z]\\:(?!\\\\)#', $path) === 0);
    }

}