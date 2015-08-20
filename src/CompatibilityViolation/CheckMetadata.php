<?php

namespace Sstalle\php7cc\CompatibilityViolation;

class CheckMetadata
{
    /**
     * @var float
     */
    protected $startTime;

    /**
     * @var float|null
     */
    protected $endTime;

    /**
     * @var int
     */
    protected $checkedFileCount = 0;

    public function __construct()
    {
        $this->startTime = microtime(true);
    }

    public function endCheck()
    {
        $this->endTime = microtime(true);
    }

    /**
     * @return float In seconds
     */
    public function getElapsedTime()
    {
        $endTime = $this->endTime;
        if ($endTime === null) {
            $endTime = microtime(true);
        }

        return $endTime - $this->startTime;
    }

    /**
     * @return int
     */
    public function getCheckedFileCount()
    {
        return $this->checkedFileCount;
    }

    public function incrementCheckedFileCount()
    {
        ++$this->checkedFileCount;
    }
}
