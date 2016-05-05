<?php

namespace Sstalle\php7cc;

use Sstalle\php7cc\CompatibilityViolation\CheckMetadata;
use Sstalle\php7cc\CompatibilityViolation\FileContext;
use Symfony\Component\Finder\SplFileInfo;

class PathChecker
{
    /**
     * @var ContextChecker
     */
    protected $contextChecker;

    /**
     * @var ResultPrinterInterface
     */
    protected $resultPrinter;

    /**
     * @param ContextChecker         $fileChecker
     * @param ResultPrinterInterface $resultPrinter
     */
    public function __construct(ContextChecker $fileChecker, ResultPrinterInterface $resultPrinter)
    {
        $this->contextChecker = $fileChecker;
        $this->resultPrinter = $resultPrinter;
    }

    /**
     * @param \Traversable $traversablePaths
     * @param bool         $useRelativePaths
     */
    public function check(\Traversable $traversablePaths, $useRelativePaths)
    {
        $checkMetadata = new CheckMetadata();

        /** @var SplFileInfo $fileInfo */
        foreach ($traversablePaths as $fileInfo) {
            $this->checkFile($checkMetadata, $fileInfo, $useRelativePaths);
        }

        $checkMetadata->endCheck();

        $this->resultPrinter->printMetadata($checkMetadata);
    }

    /**
     * @param CheckMetadata $checkMetadata
     * @param SplFileInfo   $fileInfo
     * @param bool          $useRelativePaths
     */
    protected function checkFile(CheckMetadata $checkMetadata, SplFileInfo $fileInfo, $useRelativePaths)
    {
        $context = new FileContext($fileInfo, $useRelativePaths);
        $this->contextChecker->checkContext($context);

        if ($context->hasMessagesOrErrors()) {
            $this->resultPrinter->printContext($context);
        }

        $checkMetadata->incrementCheckedFileCount();
    }
}
