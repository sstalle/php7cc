<?php

namespace code\Iterator;

use org\bovigo\vfs\vfsStream;

abstract class AbstractFilteringIteratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider filterFilesProvider
     */
    public function testFiltersFiles($directoryConfig, $filterArguments, $expectedResult)
    {
        $dir = vfsStream::setup('root', null, $directoryConfig);
        $directoryIterator = new \RecursiveDirectoryIterator(
            $dir->url(),
            \RecursiveDirectoryIterator::CURRENT_AS_FILEINFO
            | \RecursiveDirectoryIterator::KEY_AS_PATHNAME
            | \RecursiveDirectoryIterator::SKIP_DOTS
            | \RecursiveDirectoryIterator::UNIX_PATHS
        );

        $iteratorClassReflection = new \ReflectionClass($this->getIteratorClass());
        /** @var \RecursiveFilterIterator $filteringIterator */
        array_unshift($filterArguments, $directoryIterator);
        $filteringIterator = $iteratorClassReflection->newInstanceArgs($filterArguments);
        $actualResult = array();

        foreach (
            new \RecursiveIteratorIterator($filteringIterator, \RecursiveIteratorIterator::LEAVES_ONLY)
            as $fileName
        ) {
            $actualResult[] = pathinfo($fileName, PATHINFO_BASENAME);
        }

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @dataProvider throwsExceptionForInnerIteratorInvalidFlagsProvider
     */
    public function testExceptionsForInnerIteratorFlags($flags, $expectException)
    {
        $dir = vfsStream::setup('root', null, array());
        $directoryIterator = new \RecursiveDirectoryIterator(
            $dir->url(),
            $flags
        );

        if ($expectException) {
            $this->setExpectedException('\\InvalidArgumentException');
        }

        $iteratorClassReflection = new \ReflectionClass($this->getIteratorClass());
        $iteratorClassReflection->newInstanceArgs(
            array_merge(array($directoryIterator), $this->getDefaultConstructorArguments())
        );
    }

    public function throwsExceptionForInnerIteratorInvalidFlagsProvider()
    {
        return array(
            array(
                \RecursiveDirectoryIterator::CURRENT_AS_PATHNAME
                | \RecursiveDirectoryIterator::KEY_AS_PATHNAME,
                true,
            ),
            array(
                \RecursiveDirectoryIterator::CURRENT_AS_SELF
                | \RecursiveDirectoryIterator::KEY_AS_PATHNAME,
                true,
            ),
            array(
                \RecursiveDirectoryIterator::CURRENT_AS_FILEINFO
                | \RecursiveDirectoryIterator::KEY_AS_FILENAME,
                true,
            ),
            array(
                \RecursiveDirectoryIterator::CURRENT_AS_PATHNAME
                | \RecursiveDirectoryIterator::KEY_AS_FILENAME,
                true,
            ),
            array(
                \RecursiveDirectoryIterator::CURRENT_AS_FILEINFO
                | \RecursiveDirectoryIterator::KEY_AS_PATHNAME,
                false,
            ),
        );
    }

    /**
     * @return array
     */
    abstract public function filterFilesProvider();

    /**
     * @return string
     */
    abstract public function getIteratorClass();

    /**
     * @return array
     */
    abstract public function getDefaultConstructorArguments();
}
