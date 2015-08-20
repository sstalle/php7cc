<?php

namespace Sstalle\php7cc;

class File extends \SplFileInfo
{
    /**
     * @return string
     */
    public function getContents()
    {
        return file_get_contents($this->getPathname());
    }
}
