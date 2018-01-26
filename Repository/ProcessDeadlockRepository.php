<?php

namespace Repository;

use Service\Logger;
use Service\LoggerInterface;
use ValueObject\Connection;

class ProcessDeadlockRepository implements ProcessDeadlockRepositoryInterface
{
    const TABLE_NAME = 'process_dead_lock';

    const ID = 'id';
    const PROCESS_NAME = 'processName';
    const IS_LOCKED = 'isLocked';

    /** @var \mysqli */
    protected $connection;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * EventRepository constructor.
     *
     * @param Connection      $connection
     * @param LoggerInterface $logger
     */
    public function __construct(
        Connection $connection,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->connection = new \mysqli(
            $connection->getHost(),
            $connection->getUsername(),
            $connection->getPassword(),
            $connection->getDatabase()
        );

        if ($this->connection->connect_error) {
            $this->logger->log(
                'Connection failed: ' . $this->connection->connect_error,
                LoggerInterface::ALERT
            );
            exit();
        }

        if (!$this->isTableExists()) {
            $this->createTable();
        }
    }

    /**
     * @param string $processName
     *
     * @return bool
     */
    public function isReleased($processName)
    {
        $result = $this->connection->query("
                SELECT " . self::IS_LOCKED . " 
                FROM " . self::TABLE_NAME . "
                WHERE " . self::PROCESS_NAME . " = '" . $processName . "';");

        $this->checkError();

        if ($result->num_rows == 0) {

            $this->connection->query("
               INSERT INTO " . self::TABLE_NAME . "
               (" . self::IS_LOCKED . ", " . self::PROCESS_NAME . ") VALUES 
               ( 0, '" . $processName . "' )
           ");

            $this->checkError();
        }

        $row = $result->fetch_assoc();

        return !isset($row[self::IS_LOCKED]) || $row[self::IS_LOCKED] == 0;
    }

    /**
     * @param string $processName
     */
    public function lock($processName)
    {
        $this->connection->query(
            "
                    UPDATE " . self::TABLE_NAME . "
                    SET " . self::IS_LOCKED . " = 1
                    WHERE " . self::PROCESS_NAME . "='" . $processName . "';"
        );

        $this->checkError();
    }

    /**
     * @param string $processName
     */
    public function release($processName)
    {
        $this->connection->query(
            "
                    UPDATE " . self::TABLE_NAME . "
                    SET " . self::IS_LOCKED . " = 0
                    WHERE " . self::PROCESS_NAME . "='" . $processName . "';"
        );

        $this->checkError();
    }

    /**
     * @return bool
     */
    public function isTableExists()
    {
        $result = $this->connection->query("SELECT 
                  table_name 
              FROM 
                  information_schema.tables 
              WHERE 
                  table_schema = '" . DATABASE_NAME . "' 
                  AND table_name = '" . self::TABLE_NAME . "';"
        );

        return $result->num_rows > 0;
    }

    public function createTable()
    {
        $this->connection->query("
            CREATE TABLE IF NOT EXISTS " . self::TABLE_NAME . " (
                " . self::ID . " INT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
                " . self::PROCESS_NAME . " VARCHAR(50) NOT NULL,
                " . self::IS_LOCKED . " INTEGER(1) NOT NULL DEFAULT 0
            )                
        ");

        $this->checkError();
    }

    /**
     * @return bool
     */
    public function checkError()
    {
        if ($this->connection->errno) {
            $this->logger->log($this->connection->error, Logger::ALERT);
        }

        return true;
    }
}
