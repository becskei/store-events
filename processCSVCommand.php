<?php

use Validation\EventValidator;
use Service\FileHandler;
use Service\EventService;
use Service\Logger;
use Repository\EventRepository;
use ValueObject\Connection;
use Service\ProcessDeadlockService;
use Repository\ProcessDeadlockRepository;

require 'config.php';
require 'autoload.php';

$connection = new Connection(
    DATABASE_HOST,
    DATABASE_USER,
    DATABASE_PASSWORD,
    DATABASE_NAME
);

$logger = new Logger(LOG_FILE, new FileHandler());

$processDeadLock = new ProcessDeadlockService(
    new ProcessDeadlockRepository($connection, $logger),
    'PROCESS_UPLOADED_EVENTS'
);

if ($processDeadLock->isReleased()) {

    $processDeadLock->lock();

    $eventHandler = new EventService(
        new EventValidator(),
        new FileHandler(),
        new EventRepository($connection, new Logger(LOG_FILE, new FileHandler())),
        $logger
    );

    $eventHandler->saveFromFile();

    $processDeadLock->release();
}
