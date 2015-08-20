<?php

class WindowsPathHelperTest extends \code\Helper\Path\AbstractPathHelperTest
{
    /**
     * {@inheritdoc}
     */
    public function createPathHelper()
    {
        return new \Sstalle\php7cc\Helper\Path\WindowsPathHelper();
    }

    /**
     * {@inheritdoc}
     */
    public function isAbsolutePathProvider()
    {
        return array(
            array('', false),
            array('\\\\foo', true),
            array('K:\\foo', true),
            array('k:\\foo', true),
            array('\\foo', true),
            array('foo', false),
            array('k:foo', false),
            array('K:foo\bar', false),
            array('../foo/bar', false),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isDirectoryRelativePathProvider()
    {
        return array(
            array('\\\\foo', false),
            array('K:\\foo', false),
            array('k:\\foo', false),
            array('\\foo', false),
            array('k:foo', false),
            array('K:foo\bar', false),
            array('foo', true),
            array('../foo/bar', true),
        );
    }
}
