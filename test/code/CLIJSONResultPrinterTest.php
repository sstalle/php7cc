<?php

namespace code;

use Bcn\Component\Json\Writer;
use Sstalle\php7cc\AbstractBaseMessage;
use Sstalle\php7cc\CLIJSONResultPrinter;
use Sstalle\php7cc\CompatibilityViolation\CheckMetadata;
use Sstalle\php7cc\CompatibilityViolation\Message;
use Sstalle\php7cc\CompatibilityViolation\StringContext;
use Sstalle\php7cc\Error\CheckError;
use Sstalle\php7cc\Infrastructure\CLIOutputBridge;
use Symfony\Component\Console\Output\BufferedOutput;

class CLIJSONResultPrinterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CLIJSONResultPrinter
     */
    private $printer;

    /**
     * @var BufferedOutput
     */
    private $output;

    /**
     * @dataProvider printProvider
     */
    public function testPrint(array $contexts, CheckMetadata $metadata, $expectedString)
    {
        foreach ($contexts as $context) {
            $this->printer->printContext($context);
        }
        $this->printer->printMetadata($metadata);

        $printedString = $this->output->fetch();
        $this->assertNotFalse(json_decode($printedString));
        $this->assertSame($expectedString, $printedString);
    }

    public function printProvider()
    {
        $singleMessageContext = new StringContext('', 'test.php');
        $singleMessageContext->addMessage(new Message('error', 1, AbstractBaseMessage::LEVEL_ERROR));

        $multipleMessagesContext = new StringContext('', 'multitest.php');
        $multipleMessagesContext->addMessage(new Message('info1', 1, AbstractBaseMessage::LEVEL_INFO));
        $multipleMessagesContext->addMessage(new Message('error2', 2, AbstractBaseMessage::LEVEL_ERROR));

        $singleErrorContext = new StringContext('', 'error.php');
        $singleErrorContext->addError(new CheckError('parse-error', 4));

        $nullLineErrorContext = new StringContext('', 'null.php');
        $nullLineErrorContext->addError(new CheckError('parse-error'));

        $errorsMessagesContext = new StringContext('', 'errors-messages.php');
        $errorsMessagesContext->addMessage(new Message('info1', 1, AbstractBaseMessage::LEVEL_INFO));
        $errorsMessagesContext->addMessage(new Message('error2', 2, AbstractBaseMessage::LEVEL_ERROR));
        $errorsMessagesContext->addError(new CheckError('parse-error', 3));

        return array(
            array(
                array(),
                $this->createMetadataMock(0),
                '{"errors":[],"filesChecked":0,"elapsedTime":1.2345}',
            ),
            array(
                array(
                    new StringContext('', 'test.php'),
                ),
                $this->createMetadataMock(1),
                '{"errors":[],"filesChecked":1,"elapsedTime":1.2345}',
            ),
            array(
                array(
                    $singleMessageContext,
                ),
                $this->createMetadataMock(1),
                '{"errors":[{"file":"test.php","level":"error","line":1,"text":"error","code":"test"}],"filesChecked":1,"elapsedTime":1.2345}',
            ),
            array(
                array(
                    $multipleMessagesContext,
                ),
                $this->createMetadataMock(1),
                '{"errors":[{"file":"multitest.php","level":"info","line":1,"text":"info1","code":"test"},{"file":"multitest.php","level":"error","line":2,"text":"error2","code":"test"}],"filesChecked":1,"elapsedTime":1.2345}',
            ),
            array(
                array(
                    $singleErrorContext,
                ),
                $this->createMetadataMock(1),
                '{"errors":[{"level":"parse_error","file":"error.php","line":4,"text":"parse-error"}],"filesChecked":1,"elapsedTime":1.2345}',
            ),
            array(
                array(
                    $nullLineErrorContext,
                ),
                $this->createMetadataMock(1),
                '{"errors":[{"level":"parse_error","file":"null.php","line":null,"text":"parse-error"}],"filesChecked":1,"elapsedTime":1.2345}',
            ),
            array(
                array(
                    $errorsMessagesContext,
                ),
                $this->createMetadataMock(1),
                '{"errors":[{"file":"errors-messages.php","level":"info","line":1,"text":"info1","code":"test"},{"file":"errors-messages.php","level":"error","line":2,"text":"error2","code":"test"},{"level":"parse_error","file":"errors-messages.php","line":3,"text":"parse-error"}],"filesChecked":1,"elapsedTime":1.2345}',
            ),
            array(
                array(
                    $errorsMessagesContext,
                    $singleErrorContext,
                    $singleMessageContext,
                ),
                $this->createMetadataMock(3),
                '{"errors":[{"file":"errors-messages.php","level":"info","line":1,"text":"info1","code":"test"},{"file":"errors-messages.php","level":"error","line":2,"text":"error2","code":"test"},{"level":"parse_error","file":"errors-messages.php","line":3,"text":"parse-error"},{"level":"parse_error","file":"error.php","line":4,"text":"parse-error"},{"file":"test.php","level":"error","line":1,"text":"error","code":"test"}],"filesChecked":3,"elapsedTime":1.2345}',
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $nodePrinter = $this->getMockBuilder('Sstalle\\php7cc\\NodePrinterInterface')
            ->setMethods(array('printNodes'))
            ->getMock();
        $nodePrinter->method('printNodes')->willReturn('test');

        stream_wrapper_register('output', 'Sstalle\\php7cc\\OutputStreamWrapper');
        $this->output = $this->getMockBuilder('Symfony\\Component\\Console\\Output\\BufferedOutput')
            ->setMethods(array('writeln'))
            ->getMock();
        $context = stream_context_create(array('output' => array('output' => $this->output)));
        $writer = new Writer(fopen('output://test', 'a', null, $context));

        $this->printer = new CLIJSONResultPrinter(new CLIOutputBridge($this->output), $nodePrinter, $writer);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        stream_wrapper_unregister('output');
    }

    /**
     * @param $checkedFileCount
     *
     * @return CheckMetadata
     */
    protected function createMetadataMock($checkedFileCount)
    {
        $metadataMock = $this->getMockBuilder('Sstalle\\php7cc\\CompatibilityViolation\\CheckMetadata')
            ->setMethods(array('getElapsedTime', 'getCheckedFileCount'))
            ->getMock();
        $metadataMock->method('getElapsedTime')
            ->willReturn(1.2345);
        $metadataMock->method('getCheckedFileCount')
            ->willReturn($checkedFileCount);

        return $metadataMock;
    }
}
