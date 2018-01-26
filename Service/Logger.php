<?php

namespace Service;

class Logger implements LoggerInterface
{
    /** @var  string */
    protected $logFile;

    /** @var  FileHandlerInterface */
    protected $fileHandler;

    /**
     * Logger constructor.
     *
     * @param string               $logFile
     * @param FileHandlerInterface $fileHandler
     */
    public function __construct(
        $logFile,
        FileHandlerInterface $fileHandler
    ) {
        $this->logFile = $logFile;
        $this->fileHandler = $fileHandler;
    }

    /**
     * @param string $message
     * @param string $level
     */
    public function log($message, $level)
    {
        $dateTime = new \DateTime();

        $logMessage =
            '[' . $dateTime->format('Y-m-d H:s:i') . '] '
            . $level
            . ' ' . $message
            . "\n";

        $this->fileHandler->write($this->logFile, $logMessage);
    }
}
