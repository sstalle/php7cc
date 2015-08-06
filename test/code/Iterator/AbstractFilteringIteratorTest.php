<?php

namespace code\Iterator;

use org\bovigo\vfs\vfsStream;

abstract class AbstractFilteringIteratorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider filterFilesProvider
     */
    public function testFilterFiles($directoryConfig, $filterArguments, $expectedResult)
    {
        $dir = vfsStream::setup('root', null, $directoryConfig);
        $directoryIterator = new \RecursiveDirectoryIterator(
            $dir->url(),
            \RecursiveDirectoryIterator::CURRENT_AS_PATHNAME
            | \RecursiveDirectoryIterator::SKIP_DOTS
        );

        $iteratorClassReflection = new \ReflectionClass($this->getIteratorClass());
        /** @var \RecursiveFilterIterator $filteringIterator */
        array_unshift($filterArguments, $directoryIterator);
        $filteringIterator = $iteratorClassReflection->newInstanceArgs($filterArguments);
        $actualResult = array();

        foreach (new \RecursiveIteratorIterator($filteringIterator, \RecursiveIteratorIterator::LEAVES_ONLY) as $fileName) {
            $actualResult[] = pathinfo($fileName, PATHINFO_BASENAME);
        }

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @return array
     */
    abstract public function filterFilesProvider();

    /**
     * @return string
     */
    abstract public function getIteratorClass();

}