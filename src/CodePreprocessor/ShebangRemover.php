<?php

namespace Sstalle\php7cc\CodePreprocessor;

class ShebangRemover implements PreprocessorInterface
{
    const SHEBANG_REGEXP = '/\A#!.*\r?\n/';

    /**
     * {@inheritdoc}
     */
    public function preprocess($code)
    {
        $matches = array();
        preg_match(static::SHEBANG_REGEXP, $code, $matches);

        return $matches ? substr($code, strlen($matches[0])) : $code;
    }
}
