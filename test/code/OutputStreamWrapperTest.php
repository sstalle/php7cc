<?php

class OutputStreamWrapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var resource
     */
    private $context;

    /**
     * @var \Symfony\Component\Console\Output\BufferedOutput
     */
    private $output;

    public function testOpenSuccessfully()
    {
        $this->assertTrue($this->openNewOutputStream() !== false);
    }

    public function testWrite()
    {
        $testData = 'test1234';
        $output = $this->openNewOutputStream();

        fwrite($output, $testData);
        $this->assertSame($testData, $this->output->fetch());
    }

    /**
     * @dataProvider incorrectModeProvider
     */
    public function testIncorrectMode($mode)
    {
        $this->assertFalse(@fopen('output://test', $mode, null, $this->context));
    }

    public function testNoOutput()
    {
        $this->assertFalse(@fopen('output://test', 'a', null, stream_context_create(array())));
    }

    /**
     * @dataProvider incorrectModeProvider
     */
    public function testIncorrectOutput($output)
    {
        $context = stream_context_create(array('output' => array('output' => $output)));
        $this->assertFalse(@fopen('output://test', 'a', null, $context));
    }

    public function incorrectModeProvider()
    {
        return array(
            array('r'),
            array('r+'),
            array('w'),
            array('w+'),
            array('a+'),
            array('x'),
            array('x+'),
            array('c'),
            array('c+'),
        );
    }

    public function incorrectOutputProvider()
    {
        return array(
            array(1),
            array(new \stdClass()),
            array(array()),
        );
    }

    /**
     * @return resource
     */
    protected function openNewOutputStream()
    {
        return fopen('output://test', 'a', null, $this->context);
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        stream_wrapper_register('output', 'Sstalle\\php7cc\\OutputStreamWrapper');
        $this->output = new \Symfony\Component\Console\Output\BufferedOutput();
        $this->context = stream_context_create(array('output' => array('output' => $this->output)));
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        stream_wrapper_unregister('output');
    }
}
