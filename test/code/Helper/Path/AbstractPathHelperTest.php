<?php

namespace code\Helper\Path;

use Sstalle\php7cc\Helper\Path\PathHelperInterface;

abstract class AbstractPathHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PathHelperInterface
     */
    protected $pathHelper;

    public function setUp()
    {
        $this->pathHelper = $this->createPathHelper();
    }

    /**
     * @dataProvider isAbsolutePathProvider
     */
    public function testIsAbsolutePath($path, $isAbsolute)
    {
        $this->assertSame($isAbsolute, $this->pathHelper->isAbsolute($path));
    }

    /**
     * @dataProvider isDirectoryRelativePathProvider
     */
    public function testIsDirectoryRelativePath($path, $isDirectoryRelative)
    {
        $this->assertSame($isDirectoryRelative, $this->pathHelper->isDirectoryRelative($path));
    }

    /**
     * @return array
     */
    abstract public function isAbsolutePathProvider();

    /**
     * @return array
     */
    abstract public function isDirectoryRelativePathProvider();

    /**
     * @return PathHelperInterface
     */
    abstract public function createPathHelper();
}
