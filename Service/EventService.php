<?php

namespace Service;

use Repository\EventRepositoryInterface;
use Validation\EventValidator;

class EventService
{
    const CSV = 'csv';

    const EVENT_DATE_TIME = 0;
    const EVENT_ACTION = 1;
    const EVENT_CALL_REF = 2;
    const EVENT_VALUE = 3;
    const EVENT_CURRENCY_CODE = 4;

    /** @var  EventValidator */
    protected $validator;

    /** @var  FileHandlerInterface */
    protected $fileHandler;

    /** @var  EventRepositoryInterface */
    protected $eventRepository;

    /** @var  LoggerInterface */
    protected $logger;

    /**
     * EventService constructor.
     *
     * @param EventValidator           $validator
     * @param FileHandlerInterface     $fileHandler
     * @param EventRepositoryInterface $eventRepository
     * @param LoggerInterface          $logger
     */
    public function __construct(
        EventValidator $validator,
        FileHandlerInterface $fileHandler,
        EventRepositoryInterface $eventRepository,
        LoggerInterface $logger
    ) {
        $this->validator = $validator;
        $this->fileHandler = $fileHandler;
        $this->eventRepository = $eventRepository;
        $this->logger = $logger;
    }

    public function saveFromFile()
    {
        if ($this->fileHandler->hasFiles(EVENTS_UPLOAD_FOLDER, self::CSV)) {
            $files = $this->fileHandler->getFiles(EVENTS_UPLOAD_FOLDER);

            if (!empty($files)) {

                foreach ($files as $file) {

                    $events = $this->trim($this->fileHandler->csvToArray($file));

                    array_shift($events);

                    if ($this->isValid($events, $file)) {
                        foreach ($events as $event) {
                            $this->eventRepository->save($event);
                        }
                        $this->fileHandler->move($file, EVENTS_PROCESSED_FOLDER . '/' . basename($file));
                    } else {
                        $this->fileHandler->move($file, EVENTS_FAILED_FOLDER . '/' . basename($file));
                    }
                }
            }
        }
    }

    /**
     * @param string  $file
     * @param integer $line
     */
    private function logEventValidationError($file, $line)
    {
        $this->logger->log(
            sprintf(
                'Event validation failed. File: %s Line: %d', basename($file), $line + 2
            ),
            Logger::ALERT);
    }

    /**
     * @param array  $events
     * @param string $file
     *
     * @return bool
     */
    private function isValid(array $events, $file)
    {
        $isValid = true;
        foreach ($events as $line => $event) {
            if (!$this->validator->isValid($event)) {
                $this->logEventValidationError(basename($file), $line);
                $isValid = false;
            }
        }

        return $isValid;
    }

    /**
     * @param array $events
     *
     * @return array
     */
    private function trim(array $events)
    {
        foreach ($events as $key => $event) {
            if ($event[0] === null) {
                unset($events[$key]);
            }
        }

        return $events;
    }
}
