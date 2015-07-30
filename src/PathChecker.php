<?php

namespace Sstalle\php7cc;

use Sstalle\php7cc\CompatibilityViolation\CheckMetadata;
use Sstalle\php7cc\Iterator\FileDirectoryListRecursiveIterator;

class PathChecker
{

    /**
     * @var ContextChecker
     */
    protected $contextChecker;

    /**
     * @var FileContextFactory
     */
    protected $fileContextFactory;

    /**
     * @var ResultPrinterInterface
     */
    protected $resultPrinter;

    /**
     * @param ContextChecker $fileChecker
     * @param FileContextFactory $contextFactory
     * @param ResultPrinterInterface $resultPrinter
     */
    public function __construct(
        ContextChecker $fileChecker,
        FileContextFactory $contextFactory,
        ResultPrinterInterface $resultPrinter
    ) {
        $this->contextChecker = $fileChecker;
        $this->fileContextFactory = $contextFactory;
        $this->resultPrinter = $resultPrinter;
    }

    /**
     * @param string[] $paths Files and/or directories to check
     * @param string[] $checkedExtensions Only files having these extensions will be checked
     */
    public function check(array $paths, array $checkedExtensions)
    {
        foreach ($paths as $path) {
            $isPathDir = is_dir($path);

            if ($isPathDir && !$checkedExtensions) {
                throw new \DomainException('At least 1 extension should be specified to check a directory');
            } elseif ($isPathDir) {
                break;
            }
        }

        $checkMetadata = new CheckMetadata();

        $fileDirectoryIterator = new FileDirectoryListRecursiveIterator($paths);
        $recursiveIterator = new \RecursiveIteratorIterator(
            $fileDirectoryIterator,
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        /** @var \SplFileInfo $fileInfo */
        foreach ($recursiveIterator as $pathName => $fileInfo) {
            if (!in_array($fileInfo->getExtension(), $checkedExtensions)) {
                continue;
            }

            $this->checkFile($checkMetadata, $pathName);
        }

        $checkMetadata->endCheck();

        $this->resultPrinter->printMetadata($checkMetadata);
    }

    /**
     * @param CheckMetadata $checkMetadata
     * @param string $pathName
     */
    protected function checkFile(CheckMetadata $checkMetadata, $pathName)
    {
        $context = $this->fileContextFactory->createContext($pathName);
        $this->contextChecker->checkContext($context);

        if (count($context->getMessages())) {
            $this->resultPrinter->printContext($context);
        }

        $checkMetadata->incrementCheckedFileCount();
    }

}