<?php

namespace Sstalle\php7cc\CompatibilityViolation;


interface ContextInterface
{

    /**
     * @param Message $message
     */
    public function addMessage(Message $message);

    /**
     * @return Message[]
     */
    public function getMessages();

    /**
     * @return string
     */
    public function getCheckedResourceName();

    /**
     * @return string
     */
    public function getCheckedCode();

}