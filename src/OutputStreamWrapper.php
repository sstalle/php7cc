<?php

namespace Sstalle\php7cc;

use Symfony\Component\Console\Output\OutputInterface;

class OutputStreamWrapper
{
    /**
     * @var resource
     */
    public $context;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @param string $path
     * @param string $mode
     * @param int    $options
     */
    public function stream_open($path, $mode, $options)
    {
        try {
            $this->validateMode($mode);
            $this->setOutputFromContext();
        } catch (\Exception $e) {
            if ($options & STREAM_REPORT_ERRORS) {
                trigger_error($e->getMessage(), E_USER_WARNING);
            }

            return false;
        }

        return true;
    }

    /**
     * @param string $data
     */
    public function stream_write($data)
    {
        $this->output->write($data);
    }

    public function stream_flush()
    {
        return false;
    }

    public function stream_close()
    {
        $this->output = null;
    }

    /**
     * @param string $mode
     */
    protected function validateMode($mode)
    {
        if ($mode !== 'a') {
            throw new \InvalidArgumentException(sprintf('Unsupported mode %s', $mode));
        }
    }

    protected function setOutputFromContext()
    {
        $contextOptions = stream_context_get_options($this->context);
        if (!isset($contextOptions['output']['output'])) {
            throw new \LogicException('Required option "output" was not passed to context');
        }

        $output = $contextOptions['output']['output'];
        if (!($output instanceof OutputInterface)) {
            throw new \UnexpectedValueException('Output must implement Symfony\\Component\\Console\\Output\\OutputInterface');
        }

        $this->output = $output;
    }
}
