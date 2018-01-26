<?php

namespace Service;

interface LoggerInterface
{
    const INFO = 'INFO';
    const ALERT = 'ALERT';

    /**
     * @param string $message
     * @param string $level
     */
    public function log($message, $level);
}
