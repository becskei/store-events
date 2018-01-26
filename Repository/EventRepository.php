<?php

namespace Repository;

use Service\EventService;
use Service\Logger;
use Service\LoggerInterface;
use ValueObject\Connection;

class EventRepository implements EventRepositoryInterface
{
    const EVENTS_TABLE_NAME = 'events';

    const EVENT_TABLE_ID = 'id';
    const EVENT_TABLE_DATETIME = 'eventDatetime';
    const EVENT_TABLE_EVENT_ACTION = 'eventAction';
    const EVENT_TABLE_CALL_REF = 'callRef';
    const EVENT_TABLE_EVENT_VALUE = 'eventValue';
    const EVENT_TABLE_EVENT_CURRENCY_CODE = 'eventCurrencyCode';

    /** @var LoggerInterface */
    protected $logger;

    /** @var Connection */
    protected $connection;

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

        if (!$this->isTableExist()) {
            $this->createTable();
        }
    }

    /**
     * @param array $event
     *
     * @return bool
     */
    public function save(array $event)
    {
        $sql = "
            INSERT INTO " . self::EVENTS_TABLE_NAME . "
            (
                " . self::EVENT_TABLE_DATETIME . ",
                " . self::EVENT_TABLE_EVENT_ACTION . ",
                " . self::EVENT_TABLE_CALL_REF . ",
                " . self::EVENT_TABLE_EVENT_VALUE . ",
                " . self::EVENT_TABLE_EVENT_CURRENCY_CODE . "
            ) 
            VALUES 
            (
                '" . $event[EventService::EVENT_DATE_TIME] . "',
                '" . $event[EventService::EVENT_ACTION] . "',
                " . $event[EventService::EVENT_CALL_REF] . ",
                " . $event[EventService::EVENT_VALUE] . ",
                '" . $event[EventService::EVENT_CURRENCY_CODE] . "'
            )";

        $this->connection->query($sql);

        return $this->checkError();
    }

    /**
     * @return bool
     */
    private function isTableExist()
    {
        $result = $this->connection->query("SELECT 
                  table_name 
              FROM 
                  information_schema.tables 
              WHERE 
                  table_schema = '" . DATABASE_NAME . "' 
                  AND table_name = '" . self::EVENTS_TABLE_NAME . "';"
        );

        return $result->num_rows > 0;
    }

    private function createTable()
    {
        $this->connection->query("
            CREATE TABLE IF NOT EXISTS " . self::EVENTS_TABLE_NAME . " (
                " . self::EVENT_TABLE_ID . " INT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
                " . self::EVENT_TABLE_DATETIME . " DATETIME NOT NULL,
                " . self::EVENT_TABLE_EVENT_ACTION . " VARCHAR(20) NOT NULL,
                " . self::EVENT_TABLE_CALL_REF . " INTEGER NOT NULL,
                " . self::EVENT_TABLE_EVENT_VALUE . " DECIMAL(6,2),
                " . self::EVENT_TABLE_EVENT_CURRENCY_CODE . " VARCHAR(3)
            )                
        ");

        $this->checkError();
    }

    /**
     * @return bool
     */
    private function checkError()
    {
        if ($this->connection->errno) {
            $this->logger->log($this->connection->error, Logger::ALERT);
        }

        return true;
    }
}
