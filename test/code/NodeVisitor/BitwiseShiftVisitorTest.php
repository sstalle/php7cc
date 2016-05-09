<?php

namespace code\NodeVisitor;

use PhpParser\Node\Expr;
use PhpParser\Node\Scalar\LNumber;
use Sstalle\php7cc\CompatibilityViolation\StringContext;

class BitwiseShiftVisitorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider testThrowsExceptionForInvalidIntSizeProvider
     */
    public function testThrowsExceptionForInvalidIntSize($intSize, $isSizeValid)
    {
        if (!$isSizeValid) {
            $this->setExpectedException('\InvalidArgumentException');
        }

        new \Sstalle\php7cc\NodeVisitor\BitwiseShiftVisitor($intSize);
    }

    /**
     * @dataProvider testDetectsShiftsLargerThanIntSizeProvider
     */
    public function testDetectsShiftsLargerThanIntSize($intSize, $node, $expectedMessageCount)
    {
        $visitor = new \Sstalle\php7cc\NodeVisitor\BitwiseShiftVisitor($intSize);
        $testContext = new StringContext('', 'test');
        $visitor->initializeContext($testContext);
        $visitor->enterNode($node);

        $this->assertEquals($expectedMessageCount, count($testContext->getMessages()));
    }

    public function testThrowsExceptionForInvalidIntSizeProvider()
    {
        return array(
            array(0, false),
            array(-5, false),
            array(-1, false),
            array(8, true),
            array(32, true),
        );
    }

    public function testDetectsShiftsLargerThanIntSizeProvider()
    {
        $data = array();

        foreach (array(16, 32, 64) as $intSize) {
            foreach (array(8, 16, 32, 64, 128) as $shiftWidth) {
                $data[] = array(
                    $intSize,
                    new Expr\BinaryOp\ShiftLeft(new LNumber(1), new LNumber($shiftWidth)),
                    $shiftWidth >= $intSize ? 1 : 0,
                );

                $data[] = array(
                    $intSize,
                    new Expr\BinaryOp\ShiftRight(new LNumber(1), new LNumber($shiftWidth)),
                    $shiftWidth >= $intSize ? 1 : 0,
                );
            }
        }

        return $data;
    }
}
