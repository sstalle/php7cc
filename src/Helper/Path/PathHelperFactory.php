<?php

namespace Sstalle\php7cc\Helper\Path;

use Sstalle\php7cc\Helper\OSDetector;

class PathHelperFactory
{
    /**
     * @var OSDetector
     */
    protected $osDetector;

    /**
     * @param OSDetector $osDetector
     */
    public function __construct(OSDetector $osDetector)
    {
        $this->osDetector = $osDetector;
    }

    /**
     * @return PathHelperInterface
     */
    public function createPathHelper()
    {
        return $this->osDetector->isWindows() ? new WindowsPathHelper() : new UnixPathHelper();
    }
}
