<?php

namespace Sstalle\php7cc;

use Sstalle\php7cc\CompatibilityViolation\CheckMetadata;

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
     * @param \Traversable $traversablePaths
     */
    public function check(\Traversable $traversablePaths)
    {
        $checkMetadata = new CheckMetadata();

        /** @var \SplFileInfo $fileInfo */
        foreach ($traversablePaths as $pathName => $fileInfo) {
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

        if ($context->hasMessagesOrErrors()) {
            $this->resultPrinter->printContext($context);
        }

        $checkMetadata->incrementCheckedFileCount();
    }

}