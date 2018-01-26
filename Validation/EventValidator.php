<?php

namespace Validation;

use Service\EventService;

class EventValidator
{
    /**
     * @param array $event
     *
     * @return bool
     */
    public function isValid(array $event)
    {
        return $this->isEventDateTimeValid($event)
            && $this->isEventActionValid($event)
            && $this->isCallRefValid($event)
            && $this->isEventValueValid($event)
            && $this->isEventCurrencyCodeValid($event);
    }

    /**
     * @param array $event
     *
     * @return bool
     */
    public function isEventDateTimeValid(array $event)
    {
        return isset($event[EventService::EVENT_DATE_TIME])
            && !empty($event[EventService::EVENT_DATE_TIME])
            && preg_match('/^\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}$/', $event[EventService::EVENT_DATE_TIME]);
    }

    /**
     * @param array $event
     *
     * @return bool
     */
    public function isEventActionValid($event)
    {
        if (!isset($event[EventService::EVENT_ACTION])) {
            return false;
        }

        $eventActionLength = strlen($event[EventService::EVENT_ACTION]);

        return $eventActionLength > 0 && $eventActionLength <= 20;
    }

    /**
     * @param array $event
     *
     * @return bool
     */
    public function isCallRefValid(array $event)
    {
        return preg_match('/^\-?\d+$/', $event[EventService::EVENT_CALL_REF]);
    }

    /**
     * @param array $event
     *
     * @return bool
     */
    public function isEventValueValid(array $event)
    {
        return empty($event[EventService::EVENT_VALUE]) || preg_match('/^\d+\.\d{2}$/',
                $event[EventService::EVENT_VALUE]);
    }

    /**
     * @param array $event
     *
     * @return bool
     */
    public function isEventCurrencyCodeValid(array $event)
    {
        return $event[EventService::EVENT_VALUE] == 0 || preg_match('/^[a-zA-Z]{3}$/',
                $event[EventService::EVENT_CURRENCY_CODE]);
    }

}
