<?php

namespace code\Infrastructure;

use Sstalle\php7cc\Infrastructure\CLIOutputBridge;
use Symfony\Component\Console\Output\OutputInterface;

class CLIOutputBridgeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CLIOutputBridge
     */
    protected $bridge;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|OutputInterface
     */
    protected $output;

    public function setUp()
    {
        $this->output = $this->getMock('Symfony\Component\Console\Output\OutputInterface');
        $this->bridge = new CLIOutputBridge($this->output);
    }

    public function testWrite()
    {
        foreach (array('write', 'writeln') as $methodName) {
            $this->output->expects($this->once())
                ->method($methodName)
                ->with($this->equalTo($methodName));

            if ($methodName === 'write') {
                $this->bridge->write($methodName);
            } else {
                $this->bridge->writeln($methodName);
            }
        }
    }
}
