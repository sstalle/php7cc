<?php

namespace Sstalle\php7cc\Iterator;

if (!function_exists('Sstalle\php7cc\Iterator\realpath')) {
    function realpath($path)
    {
        return $path;
    }
}
