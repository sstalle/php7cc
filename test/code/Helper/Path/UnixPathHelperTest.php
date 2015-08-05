<?php

class UnixPathHelperTest extends \code\Helper\Path\AbstractPathHelperTest
{

    public function createPathHelper()
    {
        return new \Sstalle\php7cc\Helper\Path\UnixPathHelper();
    }

    public function isAbsolutePathProvider()
    {
        return array(
            array('', false),
            array('/', true),
            array('/foo', true),
            array('/foo/bar', true),
            array('foo/bar', false),
            array('foo', false),
            array('../foo', false),
        );
    }

}