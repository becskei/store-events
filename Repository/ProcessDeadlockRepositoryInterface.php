<?php

namespace Repository;

interface ProcessDeadlockRepositoryInterface
{
    /**
     * @param string $processName
     *
     * @return bool
     */
    public function isReleased($processName);

    /**
     * @param string $processName
     */
    public function lock($processName);

    /**
     * @param string $processName
     */
    public function release($processName);

    /**
     * @return bool
     */
    public function isTableExists();

    public function createTable();

    public function checkError();
}
