<?php

namespace Sstalle\php7cc\Helper;

class OSDetector
{
    public function isWindows()
    {
        return DIRECTORY_SEPARATOR == '\\';
    }
}
