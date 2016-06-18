<?php

namespace code\CompatibilityViolation;

use Sstalle\php7cc\CompatibilityViolation\CheckMetadata;

class CheckMetadataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CheckMetadata
     */
    protected $metadata;

    public function setUp()
    {
        $this->metadata = new CheckMetadata();
    }

    /**
     * @dataProvider testCheckedFileCountProvider
     */
    public function testCheckedFileCount($expectedCount)
    {
        for ($i = 0; $i < $expectedCount; ++$i) {
            $this->metadata->incrementCheckedFileCount();
        }

        $this->assertEquals($expectedCount, $this->metadata->getCheckedFileCount());
    }

    /**
     * @return array
     */
    public function testCheckedFileCountProvider()
    {
        return array(
            array(0),
            array(1),
            array(5),
        );
    }
}
