<?php

namespace Sstalle\php7cc;

use Sstalle\php7cc\CompatibilityViolation\CheckMetadata;
use Sstalle\php7cc\Iterator\ExtensionFilteringRecursiveIterator;

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
    public function __construct(ContextChecker $fileChecker, FileContextFactory $contextFactory, ResultPrinterInterface $resultPrinter)
    {
        $this->contextChecker = $fileChecker;
        $this->fileContextFactory = $contextFactory;
        $this->resultPrinter = $resultPrinter;
    }

    /**
     * @param string $path File or directory to check
     * @param string[] $checkedExtensions Only files having these extensions will be checked
     */
    public function check($path, array $checkedExtensions)
    {
        $isPathDir = is_dir($path);
        $isPathFile = is_file($path);
        if (!$isPathDir && !$isPathFile) {
            throw new \InvalidArgumentException(sprintf('Path %s is not a file or a directory', $path));
        }

        if (!$isPathFile && !$checkedExtensions) {
            throw new \DomainException('At least 1 extension should be specified to check a directory');
        }

        $checkMetadata = new CheckMetadata();

        if (is_file($path)) {
            $context = $this->fileContextFactory->createContext($path);
            $this->contextChecker->checkContext($context);
            $this->resultPrinter->printContext($context);

            $checkMetadata->incrementCheckedFileCount();
        } else {
            $directoryIterator = new \RecursiveDirectoryIterator(
                $path,
                \RecursiveDirectoryIterator::KEY_AS_PATHNAME
                | \RecursiveDirectoryIterator::CURRENT_AS_PATHNAME
                | \RecursiveDirectoryIterator::SKIP_DOTS
            );
            $extensionFilteringIterator = new ExtensionFilteringRecursiveIterator(
                $directoryIterator,
                $checkedExtensions
            );
            $recursiveIterator = new \RecursiveIteratorIterator(
                $extensionFilteringIterator,
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($recursiveIterator as $pathName) {
                $context = $this->fileContextFactory->createContext($pathName);
                $this->contextChecker->checkContext($context);

                if (count($context->getMessages())) {
                    $this->resultPrinter->printContext($context);
                }

                $checkMetadata->incrementCheckedFileCount();
            }
        }

        $checkMetadata->endCheck();

        $this->resultPrinter->printMetadata($checkMetadata);
    }

}