<?php

namespace Sstalle\php7cc\Helper;

class OSDetector
{
    /**
     * @return bool
     */
    public function isWindows()
    {
        return DIRECTORY_SEPARATOR == '\\';
    }
}
