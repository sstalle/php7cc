<?php

namespace Sstalle\php7cc;

use Sstalle\php7cc\CompatibilityViolation\Message;

class PathCheckSettings
{
    /**
     * @var array
     */
    protected $checkedPaths;

    /**
     * @var array
     */
    protected $checkedFileExtensions;

    /**
     * @var array
     */
    protected $excludedPaths = array();

    /**
     * @var int
     */
    protected $messageLevel = Message::LEVEL_INFO;

    /**
     * @var bool
     */
    protected $useRelativePaths = false;

    /**
     * @param array $checkedPaths
     * @param array $checkedFileExtensions
     */
    public function __construct(array $checkedPaths, array $checkedFileExtensions)
    {
        if (!$checkedPaths) {
            throw new \InvalidArgumentException('At least 1 path to check must be specified');
        }

        if (!$checkedFileExtensions) {
            throw new \InvalidArgumentException('At least 1 file extension to check must be specified');
        }

        $this->checkedPaths = $checkedPaths;
        $this->checkedFileExtensions = $checkedFileExtensions;
    }

    /**
     * @return array
     */
    public function getCheckedPaths()
    {
        return $this->checkedPaths;
    }

    /**
     * @return array
     */
    public function getCheckedFileExtensions()
    {
        return $this->checkedFileExtensions;
    }

    /**
     * @return array
     */
    public function getExcludedPaths()
    {
        return $this->excludedPaths;
    }

    /**
     * @param array $excludedPaths
     */
    public function setExcludedPaths($excludedPaths)
    {
        $this->excludedPaths = $excludedPaths;
    }

    /**
     * @return int
     */
    public function getMessageLevel()
    {
        return $this->messageLevel;
    }

    /**
     * @param int $messageLevel
     */
    public function setMessageLevel($messageLevel)
    {
        $this->messageLevel = $messageLevel;
    }

    /**
     * @return bool
     */
    public function getUseRelativePaths()
    {
        return $this->useRelativePaths;
    }

    /**
     * @param bool $useRelativePaths
     */
    public function setUseRelativePaths($useRelativePaths)
    {
        $this->useRelativePaths = $useRelativePaths;
    }
}
