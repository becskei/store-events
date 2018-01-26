<?php

namespace Service;

use Repository\ProcessDeadlockRepositoryInterface;

class ProcessDeadlockService
{
    /** @var ProcessDeadlockRepositoryInterface */
    protected $repository;

    /** @var string */
    protected $processName;

    /**
     * ProcessDeadlockService constructor.
     *
     * @param ProcessDeadlockRepositoryInterface $repository
     * @param string                             $processName
     */
    public function __construct(
        ProcessDeadlockRepositoryInterface $repository,
        $processName
    ) {
        $this->repository = $repository;
        $this->processName = $processName;
    }

    public function isReleased()
    {
        return $this->repository->isReleased($this->processName);
    }

    public function lock()
    {
        $this->repository->lock($this->processName);
    }

    public function release()
    {
        $this->repository->release($this->processName);
    }
}
