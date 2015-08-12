<?php

namespace code\Helper\RegExp;

use Sstalle\php7cc\Helper\RegExp\RegExp;

class RegExpTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider throwsExceptionWithEmptyDelimiterProvider
     */
    public function testThrowsExceptionWithEmptyStartDelimiter($delimiter)
    {
        new RegExp($delimiter, '/', 'abc', '');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider throwsExceptionWithEmptyDelimiterProvider
     */
    public function testThrowsExceptionWithEmptyEndDelimiter($delimiter)
    {
        new RegExp('/', $delimiter, 'abc', '');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider throwsExceptionWithInvalidDelimiterProvider
     */
    public function testThrowsExceptionWithInvalidStartDelimiter($delimiter)
    {
        new RegExp($delimiter, '/', 'abc', '');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider throwsExceptionWithInvalidDelimiterProvider
     */
    public function testThrowsExceptionWithInvalidEndDelimiter($delimiter)
    {
        new RegExp('/', $delimiter, 'abc', '');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider throwsExceptionWithNonMatchingDelimitersProvider
     */
    public function testThrowsExceptionWithNonMatchingDelimiters($startDelimiter, $endDelimiter)
    {
        new RegExp($startDelimiter, $endDelimiter, 'abc', '');
    }

    /**
     * @dataProvider hasModifierProvider
     */
    public function testHasModifier($modifiers, $testedModifier, $hasModifier)
    {
        $regexp = new RegExp('/', '/', '[abc]', $modifiers);

        $this->assertSame($hasModifier, $regexp->hasModifier($testedModifier));
    }

    public function throwsExceptionWithEmptyDelimiterProvider()
    {
        return array(
            array(null),
            array(''),
        );
    }

    public function throwsExceptionWithInvalidDelimiterProvider()
    {
        return array(
            array('a'),
            array('A'),
            array('0'),
            array('\\'),
            array(' '),
        );
    }

    public function throwsExceptionWithNonMatchingDelimitersProvider()
    {
        return array(
            array('/', '#'),
            array('(', '('),
            array('[', '['),
            array('{', '{'),
            array('<', '<'),
        );
    }

    public function hasModifierProvider()
    {
        return array(
            array('abc', 'a', true),
            array('abc', 'b', true),
            array('b', 'b', true),
            array('', 'b', false),
            array('a', 'b', false),
            array('aec', 'b', false),
        );
    }

}