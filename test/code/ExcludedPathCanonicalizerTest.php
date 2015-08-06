<?php

namespace Sstalle\php7cc;

function realpath($path)
{
    return $path;
}

function is_dir($path)
{
    return $path[0] === '/';
}

class ExcludedPathCanonicalizerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider canonicalizeAbsolutePathsProvider
     */
    public function testCanonicalizeAbsolutePaths($checkedPaths, $excludedPaths, $expectedPaths)
    {
        $this->canonicalize(false, $checkedPaths, $excludedPaths, $expectedPaths);
    }

    /**
     * @dataProvider canonicalizeRelativePathsProvider
     */
    public function testCanonicalizeRelativePaths($checkedPaths, $excludedPaths, $expectedPaths)
    {
        $this->canonicalize(true, $checkedPaths, $excludedPaths, $expectedPaths);
    }

    protected function canonicalize(
        $isDirectoryRelative,
        $checkedPaths,
        $excludedPaths,
        $expectedPaths
    ) {
        $stub = $this->getMockBuilder('Sstalle\\php7cc\\Helper\\Path\\PathHelperInterface')
            ->getMock();
        $stub->method('isDirectoryRelative')
            ->willReturn($isDirectoryRelative);
        $canonicalizer = new ExcludedPathCanonicalizer($stub);

        $this->assertEquals($expectedPaths, $canonicalizer->canonicalize($checkedPaths, $excludedPaths));
    }

    public function canonicalizeAbsolutePathsProvider()
    {
        return array(
            array(
                array('/foo'),
                array(),
                array()
            ),
            array(
                array('/foo', '/bar'),
                array('baz'),
                array('baz')
            ),
            array(
                array('/foo', '/bar'),
                array('baz', 'quux'),
                array('baz', 'quux')
            ),
            array(
                array(),
                array('bar', 'baz'),
                array('bar', 'baz')
            ),
            array(
                array('foo', 'bar'),
                array('baz', 'quux'),
                array('baz', 'quux')
            ),
        );
    }

    public function canonicalizeRelativePathsProvider()
    {
        return array(
            array(
                array('/foo'),
                array(),
                array()
            ),
            array(
                array('/foo'),
                array('bar'),
                array(
                    $this->implodeWithDirectorySeparator(array('/foo', 'bar'))
                )
            ),
            array(
                array('/foo', '/bar'),
                array('baz'),
                array(
                    $this->implodeWithDirectorySeparator(array('/foo', 'baz')),
                    $this->implodeWithDirectorySeparator(array('/bar', 'baz'))
                )
            ),
            array(
                array('/foo'),
                array('bar', 'baz'),
                array(
                    $this->implodeWithDirectorySeparator(array('/foo', 'bar')),
                    $this->implodeWithDirectorySeparator(array('/foo', 'baz'))
                )
            ),
            array(
                array('/foo', '/bar'),
                array('baz', 'quux'),
                array(
                    $this->implodeWithDirectorySeparator(array('/foo', 'baz')),
                    $this->implodeWithDirectorySeparator(array('/bar', 'baz')),
                    $this->implodeWithDirectorySeparator(array('/foo', 'quux')),
                    $this->implodeWithDirectorySeparator(array('/bar', 'quux'))
                )
            ),
            array(
                array('foo', 'bar'),
                array('baz', 'quux'),
                array()
            ),
            array(
                array('foo'),
                array('baz'),
                array()
            ),
        );
    }

    protected function implodeWithDirectorySeparator(array $pieces)
    {
        return implode(DIRECTORY_SEPARATOR, $pieces);
    }

}