<?php

namespace Sstalle\php7cc\Error;

use Sstalle\php7cc\AbstractBaseMessage;

class CheckError extends AbstractBaseMessage
{
    /**
     * {@inheritdoc}
     */
    protected function generateText()
    {
        $text = $this->getRawText();
        if ($this->getLine()) {
            $text = sprintf('Line %d. %s', $this->getLine(), $text);
        }

        return $text . '. Processing aborted.';
    }
}
