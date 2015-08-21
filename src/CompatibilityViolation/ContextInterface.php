<?php

namespace Sstalle\php7cc\CompatibilityViolation;

use Sstalle\php7cc\Error\CheckError;

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
     * @param CheckError $error
     */
    public function addError(CheckError $error);

    /**
     * @return CheckError[]
     */
    public function getErrors();

    /**
     * @return bool
     */
    public function hasMessagesOrErrors();

    /**
     * @return string
     */
    public function getCheckedResourceName();

    /**
     * @return string
     */
    public function getCheckedCode();
}
