<?php

namespace Sstalle\php7cc\CompatibilityViolation;

abstract class AbstractContext implements ContextInterface
{

    /**
     * @var array|Message[]
     */
    protected $messages = array();

    /**
     * @param Message $message
     */
    public function addMessage(Message $message)
    {
        $this->messages[] = $message;
    }

    /**
     * @return array|Message[]
     */
    public function getMessages()
    {
        return $this->messages;
    }

}