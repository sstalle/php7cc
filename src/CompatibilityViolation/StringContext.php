<?php

namespace Sstalle\php7cc\CompatibilityViolation;

class StringContext extends AbstractContext
{

    /**
     * @var string
     */
    protected $checkedCode;

    /**
     * @var string
     */
    protected $checkedResourceName;

    /**
     * @param string $checkedCode
     * @param string $checkedResourceName
     */
    public function __construct($checkedCode, $checkedResourceName)
    {
        $this->checkedCode = $checkedCode;
        $this->checkedResourceName = $checkedResourceName;
    }

    /**
     * @inheritDoc
     */
    public function getCheckedResourceName()
    {
        return $this->checkedResourceName;
    }

    /**
     * @inheritDoc
     */
    public function getCheckedCode()
    {
        return $this->checkedCode;
    }


}