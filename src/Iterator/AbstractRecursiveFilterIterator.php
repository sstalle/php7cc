<?php

namespace Sstalle\php7cc\Iterator;

use RecursiveIterator;

abstract class AbstractRecursiveFilterIterator extends \RecursiveFilterIterator
{
    /**
     * {@inheritdoc}
     */
    public function __construct(RecursiveIterator $iterator)
    {
        if ($iterator instanceof \RecursiveDirectoryIterator) {
            $iteratorFlags = $iterator->getFlags();
            if ($iteratorFlags & \RecursiveDirectoryIterator::CURRENT_AS_PATHNAME
                || $iteratorFlags & \RecursiveDirectoryIterator::CURRENT_AS_SELF
            ) {
                throw new \InvalidArgumentException(
                    'This iterator requires \RecursiveDirectoryIterator with CURRENT_AS_FILEINFO flag set'
                );
            }

            if ($iteratorFlags & \RecursiveDirectoryIterator::KEY_AS_FILENAME) {
                throw new \InvalidArgumentException(
                    'This iterator requires \RecursiveDirectoryIterator with KEY_AS_PATHNAME flag set'
                );
            }
        }

        parent::__construct($iterator);
    }
}
