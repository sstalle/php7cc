<?php

namespace Sstalle\php7cc\Iterator;

use org\bovigo\vfs\vfsStream;
use Symfony\Component\Finder\SplFileInfo;

function realpath($path)
{
    return $path;
}

class FileDirectoryListRecursiveIteratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider testRelativePathNamesProvider
     */
    public function testRelativePathNames($directoryConfig, $pathsToIterate, $expectedFileNames)
    {
        $fileSystem = vfsStream::setup('root', null, $directoryConfig);
        $pathUrls = array();
        foreach ($pathsToIterate as $path) {
            $pathUrls[] = $fileSystem->getChild($path)->url();
        }

        $i = 0;
        $iterator = new \RecursiveIteratorIterator(
            new FileDirectoryListRecursiveIterator($pathUrls),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
        /** @var SplFileInfo $fileInfo */
        foreach ($iterator as $fileInfo) {
            $this->assertEquals($expectedFileNames[$i++], $fileInfo->getRelativePathname());
        }
    }

    public function testRelativePathNamesProvider()
    {
        return array(
            array(
                array(
                    'folder' => array(
                        'subfolder' => array(
                            'test.php' => '1',
                        ),
                        'anothersubfolder' => array(
                            'test2.php' => '1',
                        ),
                    ),
                ),
                array(
                    'folder',
                ),
                array(
                    'subfolder/test.php',
                    'anothersubfolder/test2.php',
                ),
            ),
            array(
                array(
                    'folder' => array(
                        'subfolder' => array(
                            'test.php' => '1',
                        ),
                        'anothersubfolder' => array(
                            'test2.php' => '1',
                        ),
                    ),
                ),
                array(
                    'folder/subfolder/test.php',
                ),
                array(
                    'test.php',
                ),
            ),
            array(
                array(
                    'folder' => array(
                        'subfolder' => array(
                            'test.php' => '1',
                        ),
                        'anothersubfolder' => array(
                            'test2.php' => '1',
                        ),
                    ),
                    'anotherfolder' => array(
                        'test3.php' => '1',
                    ),
                ),
                array(
                    'folder/subfolder',
                    'folder/anothersubfolder/test2.php',
                ),
                array(
                    'test.php',
                    'test2.php',
                    'test3.php',
                ),
            ),
        );
    }
}
