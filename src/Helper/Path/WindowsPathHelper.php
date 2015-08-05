<?php

namespace Sstalle\php7cc\Helper\Path;

class WindowsPathHelper implements PathHelperInterface
{

    /**
     * @inheritDoc
     */
    public function isAbsolute($path)
    {
        return $path && (preg_match('#^(\\\\\\\\|[a-zA-z]\\:\\\\)|\\\\.+#', $path) === 1);
    }

}