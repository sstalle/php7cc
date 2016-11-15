<?php

namespace code\CompatibilityViolation;

use Sstalle\php7cc\AbstractBaseMessage;
use Sstalle\php7cc\CompatibilityViolation\Message;

class MessageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getMessageLevelNameProvider
     */
    public function testGetMessageLevelName($level, $levelName)
    {
        $message = new Message('test', null, $level);
        $this->assertSame($levelName, $message->getLevelName());
    }

    /**
     * @dataProvider generateTextProvider
     */
    public function testGenerateText($line, $rawText)
    {
        $message = new Message($rawText, $line);
        $this->assertSame(sprintf('Line %d. %s', $line, $rawText), $message->getText());
    }

    public function getMessageLevelNameProvider()
    {
        return array(
            array(AbstractBaseMessage::LEVEL_INFO, AbstractBaseMessage::LEVEL_NAME_INFO),
            array(AbstractBaseMessage::LEVEL_WARNING, AbstractBaseMessage::LEVEL_NAME_WARNING),
            array(AbstractBaseMessage::LEVEL_ERROR, AbstractBaseMessage::LEVEL_NAME_ERROR),
            array(AbstractBaseMessage::LEVEL_PARSE_ERROR, AbstractBaseMessage::LEVEL_NAME_PARSE_ERROR),
        );
    }

    public function generateTextProvider()
    {
        return array(
            array(0, 'test'),
            array(155, 'line'),
        );
    }
}
