<?php

namespace Sstalle\php7cc;

use Sstalle\php7cc\CompatibilityViolation\FileContext;

class FileContextFactory
{

    /**
     * @param string $file
     * @return FileContext
     */
    public function createContext($file)
    {
        $file = new File($file);

        return new FileContext($file);
    }

}