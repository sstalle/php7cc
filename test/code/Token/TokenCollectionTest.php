<?php

namespace code\Token;

use Sstalle\php7cc\Token\TokenCollection;

class TokenCollectionTest extends \PHPUnit_Framework_TestCase
{
    protected static $tokens = array('foo', 'bar', "\n", "\r\n", "\t", 'baz', 'foo1', 'bar1', 'baz1');

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testGetTokenThrowsExceptionForNonExistentIndex()
    {
        $collection = new TokenCollection(array());
        $collection->getToken(0);
    }

    public function testGetTokenReturnTokenAtCorrectIndex()
    {
        $rawTokens = self::$tokens;
        $collection = new TokenCollection($rawTokens);
        foreach ($rawTokens as $i => $rawValue) {
            $this->assertSame($rawValue, $collection->getToken($i)->__toString());
        }
    }

    /**
     * @dataProvider isTokenEqualToProvider
     */
    public function testIsTokenEqualTo($tokens, $position, $value, $isEqualTo)
    {
        $collection = new TokenCollection($tokens);
        $this->assertSame($isEqualTo, $collection->isTokenEqualTo($position, $value));
    }

    /**
     * @dataProvider isTokenPrecededByProvider
     */
    public function testIsTokenPrecededBy($tokens, $position, $value, $result)
    {
        $collection = new TokenCollection($tokens);
        $this->assertSame($result, $collection->isTokenPrecededBy($position, $value));
    }

    /**
     * @dataProvider isTokenFollowedByProvider
     */
    public function testIsTokenFollowedBy($tokens, $position, $value, $result)
    {
        $collection = new TokenCollection($tokens);
        $this->assertSame($result, $collection->isTokenFollowedBy($position, $value));
    }

    /**
     * @dataProvider isTokenEqualToOrPrecededByProvider
     */
    public function testIsTokenEqualToOrPrecededBy($tokens, $position, $value, $result)
    {
        $collection = new TokenCollection($tokens);
        $this->assertSame($result, $collection->isTokenEqualToOrPrecededBy($position, $value));
    }

    /**
     * @dataProvider isTokenEqualToOrFollowedByProvider
     */
    public function testIsTokenEqualToOrFollowedBy($tokens, $position, $value, $result)
    {
        $collection = new TokenCollection($tokens);
        $this->assertSame($result, $collection->isTokenEqualToOrFollowedBy($position, $value));
    }

    public function isTokenEqualToProvider()
    {
        $data = array();

        $tokenCount = count(self::$tokens);
        $testedIndexes = array(0, floor($tokenCount / 2), $tokenCount - 1);

        foreach ($testedIndexes as $i) {
            $data[] = array(
                self::$tokens,
                $i,
                self::$tokens[$i],
                true,
            );

            $data[] = array(
                self::$tokens,
                $i,
                self::$tokens[$i] . mt_rand(),
                false,
            );
        }

        return $data;
    }

    public function isTokenPrecededByProvider()
    {
        return array(
            array(
                self::$tokens,
                0,
                'foo',
                false,
            ),
            array(
                self::$tokens,
                1,
                'foo',
                true,
            ),
            array(
                self::$tokens,
                8,
                'foo',
                false,
            ),
            array(
                self::$tokens,
                0,
                'bar',
                false,
            ),
            array(
                self::$tokens,
                1,
                'bar',
                false,
            ),
            array(
                self::$tokens,
                2,
                'bar',
                true,
            ),
            array(
                self::$tokens,
                8,
                'bar',
                false,
            ),
            array(
                self::$tokens,
                5,
                'bar',
                true,
            ),
            array(
                self::$tokens,
                4,
                "\r\n",
                true,
            ),
            array(
                self::$tokens,
                4,
                "\t",
                false,
            ),
        );
    }

    public function isTokenFollowedByProvider()
    {
        return array(
            array(
                self::$tokens,
                0,
                'foo',
                false,
            ),
            array(
                self::$tokens,
                1,
                'foo',
                false,
            ),
            array(
                self::$tokens,
                8,
                'foo',
                false,
            ),
            array(
                self::$tokens,
                0,
                'bar',
                true,
            ),
            array(
                self::$tokens,
                1,
                'bar',
                false,
            ),
            array(
                self::$tokens,
                2,
                'bar',
                false,
            ),
            array(
                self::$tokens,
                8,
                'bar',
                false,
            ),
            array(
                self::$tokens,
                1,
                'baz',
                true,
            ),
            array(
                self::$tokens,
                2,
                "\r\n",
                true,
            ),
            array(
                self::$tokens,
                2,
                "\t",
                false,
            ),
        );
    }

    public function isTokenEqualToOrPrecededByProvider()
    {
        return $this->addEqualsToCurentConditionToPrecededOrFollowedByData($this->isTokenPrecededByProvider());
    }

    public function isTokenEqualToOrFollowedByProvider()
    {
        return $this->addEqualsToCurentConditionToPrecededOrFollowedByData($this->isTokenFollowedByProvider());
    }

    protected function addEqualsToCurentConditionToPrecededOrFollowedByData($data)
    {
        foreach ($data as &$testCase) {
            $testCase[3] = $testCase[3] || self::$tokens[$testCase[1]] === $testCase[2];
        }
        unset($testCase);

        return $data;
    }
}
