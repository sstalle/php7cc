<?php

namespace Sstalle\php7cc\CompatibilityViolation;

use Sstalle\php7cc\Error\CheckError;

abstract class AbstractContext implements ContextInterface
{
    /**
     * @var array|Message[]
     */
    protected $messages = array();

    /**
     * @var CheckError[]
     */
    protected $errors = array();

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

    /**
     * {@inheritdoc}
     */
    public function addError(CheckError $error)
    {
        $this->errors[] = $error;
    }

    /**
     * {@inheritdoc}
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * {@inheritdoc}
     */
    public function hasMessagesOrErrors()
    {
        return $this->messages || $this->errors;
    }
}
