<?php

namespace code\Helper\RegExp;

use Sstalle\php7cc\Helper\RegExp\RegExpParser;

class RegExpParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RegExpParser
     */
    protected $parser;

    public function setUp()
    {
        $this->parser = new RegExpParser();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExceptionOnEmptyRegExp()
    {
        $this->parser->parse('');
    }

    /**
     * @dataProvider throwsExceptionOnRegExpWithoutClosingDelimiterProvider
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExceptionOnRegExpWithoutClosingDelimiter($regExp)
    {
        $this->parser->parse($regExp);
    }

    /**
     * @dataProvider parsesRegExpCorrectlyProvider
     */
    public function testParsesRegExpCorrectly(
        $regExp,
        $expectedStartDelimiter,
        $expectedEndDelimiter,
        $expectedExpression,
        $expectedModifiers
    ) {
        $parsedRegExp = $this->parser->parse($regExp);

        $this->assertEquals($expectedStartDelimiter, $parsedRegExp->getStartDelimiter());
        $this->assertEquals($expectedEndDelimiter, $parsedRegExp->getEndDelimiter());
        $this->assertEquals($expectedExpression, $parsedRegExp->getExpression());
        $this->assertEquals($expectedModifiers, $parsedRegExp->getModifiers());
    }

    public function throwsExceptionOnRegExpWithoutClosingDelimiterProvider()
    {
        return array(
            array('/foo'),
            array('#foo'),
        );
    }

    public function parsesRegExpCorrectlyProvider()
    {
        return array(
            array(
                '/foo/bar',
                '/',
                '/',
                'foo',
                'bar',
            ),
            array(
                '(foo)b',
                '(',
                ')',
                'foo',
                'b',
            ),
            array(
                '#foo#',
                '#',
                '#',
                'foo',
                '',
            ),
            array(
                '{a}',
                '{',
                '}',
                'a',
                '',
            ),
            array(
                '[a]',
                '[',
                ']',
                'a',
                '',
            ),
            array(
                '<a>',
                '<',
                '>',
                'a',
                '',
            ),
        );
    }
}
