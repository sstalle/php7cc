<?php

namespace code\Iterator;

class ExtensionFilteringRecursiveIteratorTest extends AbstractFilteringIteratorTest
{

    /**
     * @inheritDoc
     */
    public function filterFilesProvider()
    {
        return array(
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
                    'topphp.php' => '1'
                ),
                array(
                    array('test')
                ),
                array(
                    'folderphp.test'
                )
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
                    'topphp.php' => '1'
                ),
                array(
                    array('php')
                ),
                array(
                    'subfolderphp.php',
                    'folderphp.php',
                    'topphp.php',
                )
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
                    'topphp.php' => '1'
                ),
                array(
                    array('php', 'test')
                ),
                array(
                    'subfolderphp.php',
                    'folderphp.php',
                    'folderphp.test',
                    'topphp.php',
                )
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
                    'topphp.php' => '1'
                ),
                array(
                    array()
                ),
                array()
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getIteratorClass()
    {
        return '\\Sstalle\\php7cc\\Iterator\\ExtensionFilteringRecursiveIterator';
    }

}