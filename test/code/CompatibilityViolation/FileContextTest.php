<?php

namespace code\CompatibilityViolation;

use Sstalle\php7cc\CompatibilityViolation\FileContext;
use Symfony\Component\Finder\SplFileInfo as BaseSplFileInfo;

class FileContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider testGetCheckedResourceNameProvider
     */
    public function testGetCheckedResourceName($fullPath, $relativePath, $useRelativePaths)
    {
        $file = new SplFileInfo($fullPath, null, $relativePath);
        $context = new FileContext($file, $useRelativePaths);

        $this->assertSame(
            $useRelativePaths ? $file->getRelativePathname() : $file->getRealPath(),
            $context->getCheckedResourceName()
        );
    }

    public function testGetCheckedResourceNameProvider()
    {
        return array(
            array('/foo/bar.php', 'bar.php', true),
            array('C:\baz.php', 'test\bar.php', true),
            array('/foo/bar/baz.php', 'test/bar.php', false),
            array('C:\bar\baz.php', 'bar.php', false),
        );
    }
}

class SplFileInfo extends BaseSplFileInfo
{
    /**
     * @var string
     */
    protected $fullPath;

    /**
     * {@inheritdoc}
     */
    public function __construct($file, $relativePath, $relativePathname)
    {
        parent::__construct($file, $relativePath, $relativePathname);
        $this->fullPath = $file;
    }

    public function getRealPath()
    {
        return $this->fullPath;
    }
}
