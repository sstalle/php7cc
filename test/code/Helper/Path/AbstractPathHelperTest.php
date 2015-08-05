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
        $this->assertSame($this->pathHelper->isAbsolute($path), $isAbsolute);
    }

    /**
     * @return array
     */
    abstract public function isAbsolutePathProvider();

    /**
     * @return PathHelperInterface
     */
    abstract public function createPathHelper();

}