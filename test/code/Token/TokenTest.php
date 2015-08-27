<?php

namespace code\Token;

use Sstalle\php7cc\Token\Token;

class TokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider throwsExceptionOnArrayTokenWithNotEnoughElementsProvider
     */
    public function testThrowsExceptionOnArrayTokenWithNotEnoughElements($rawToken)
    {
        new Token($rawToken);
    }

    /**
     * @dataProvider returnsCorrectStringValueProvider
     */
    public function testReturnsCorrectStringValue($rawToken, $stringValue)
    {
        $token = new Token($rawToken);
        $this->assertSame($stringValue, $token->__toString());
    }

    /**
     * @dataProvider comparesStringValueCorrectlyProvider
     */
    public function testComparesStringValueCorrectly($rawToken, $comparedValue, $areStringValuesEquals)
    {
        $token = new Token($rawToken);
        $this->assertSame($areStringValuesEquals, $token->isStringValueEqualTo($comparedValue));
    }

    public function throwsExceptionOnArrayTokenWithNotEnoughElementsProvider()
    {
        return array(
            array(array()),
            array(array(1)),
            array(array(1, '')),
        );
    }

    public function returnsCorrectStringValueProvider()
    {
        return array(
            array(
                'foo',
                'foo',
            ),
            array(
                array(1, 'foo', 'bar'),
                'foo',
            ),
        );
    }

    public function comparesStringValueCorrectlyProvider()
    {
        return array(
            array(
                'foo',
                'foo',
                true,
            ),
            array(
                'foo',
                'bar',
                false,
            ),
            array(
                array(1, 'foo', 'bar'),
                'foo',
                true,
            ),
            array(
                array(1, 'foo', 'bar'),
                'bar',
                false,
            ),
        );
    }
}
