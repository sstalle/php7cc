<?php

namespace code\Iterator;

class ExcludedPathFilteringRecursiveIteratorTest extends AbstractFilteringIteratorTest
{
    /**
     * {@inheritdoc}
     */
    public function filterFilesProvider()
    {
        return array(
            array(
                array(
                    'nonempty' => array(),
                    'folder' => array(
                        'subfolder' => array(
                            'subfolderphp.php' => '1',
                        ),
                        'folderphp.php' => '1',
                        'folderphp.test' => '1',
                    ),
                    'topphp.php' => '1',
                ),
                array(
                    array('vfs://root/folder'),
                ),
                array(
                    'topphp.php',
                ),
            ),
            array(
                array(
                    'empty' => array(),
                    'folder' => array(
                        'subfolder' => array(
                            'subfolderphp.php' => '1',
                        ),
                        'folderphp.php' => '1',
                        'folderphp.test' => '1',
                    ),
                    'topphp.php' => '1',
                ),
                array(
                    array('vfs://root/folder/subfolder'),
                ),
                array(
                    'folderphp.php',
                    'folderphp.test',
                    'topphp.php',
                ),
            ),
            array(
                array(
                    'empty' => array(),
                    'folder' => array(
                        'subfolder' => array(
                            'subfolderphp.php' => '1',
                        ),
                        'folderphp.php' => '1',
                        'folderphp.test' => '1',
                    ),
                    'topphp.php' => '1',
                ),
                array(
                    array(),
                ),
                array(
                    'subfolderphp.php',
                    'folderphp.php',
                    'folderphp.test',
                    'topphp.php',
                ),
            ),
            array(
                array(
                    'empty' => array(
                        'empty.php' => 'empty',
                    ),
                    'folder' => array(
                        'subfolder' => array(
                            'subfolderphp.php' => '1',
                        ),
                        'folderphp.php' => '1',
                        'folderphp.test' => '1',
                    ),
                    'topphp.php' => '1',
                ),
                array(
                    array('vfs://root/folder', 'vfs://root/empty'),
                ),
                array(
                    'topphp.php',
                ),
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getIteratorClass()
    {
        return '\\Sstalle\\php7cc\\Iterator\\ExcludedPathFilteringRecursiveIterator';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultConstructorArguments()
    {
        return array(array());
    }
}
