<?php

namespace Sstalle\php7cc;

interface CLIOutputInterface
{
    /**
     * @param string $string
     */
    public function write($string);

    /**
     * @param string $string
     */
    public function writeln($string);
}
