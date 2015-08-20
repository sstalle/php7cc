<?php

namespace Sstalle\php7cc\Helper\Path;

class WindowsPathHelper implements PathHelperInterface
{
    /**
     * {@inheritdoc}
     */
    public function isAbsolute($path)
    {
        return $path && (preg_match('#^(\\\\\\\\|[a-zA-Z]\\:\\\\|\\\\.+)#', $path) === 1);
    }

    /**
     * {@inheritdoc}
     */
    public function isDirectoryRelative($path)
    {
        return $path && (!$this->isAbsolute($path) && preg_match('#^[a-zA-Z]\\:(?!\\\\)#', $path) === 0);
    }
}
