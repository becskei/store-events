<?php

namespace Repository;

interface EventRepositoryInterface
{
    /**
     * @param array $event
     */
    public function save(array $event);

    /**
     * @return bool
     */
    public function isTableExist();

    public function createTable();
}
