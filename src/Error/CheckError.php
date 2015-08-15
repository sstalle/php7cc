<?php

namespace Sstalle\php7cc\Error;

class CheckError
{

    /**
     * @var \Exception
     */
    protected $wrappedException;

    /**
     * @param \Exception $wrappedException
     */
    public function __construct(\Exception $wrappedException)
    {
        $this->wrappedException = $wrappedException;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->wrappedException->getMessage();
    }

}