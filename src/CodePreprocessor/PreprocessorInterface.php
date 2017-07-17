<?php

namespace Sstalle\php7cc\CodePreprocessor;

interface PreprocessorInterface
{
    /**
     * @param string $code
     *
     * @return string
     */
    public function preprocess($code);
}
