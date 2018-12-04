<?php

namespace Handscube\Kernel\Events;

use Handscube\Foundations\BaseEvent;
use Handscube\Kernel\Schedules\EventSchedule;

/**
 * Class Event [c] Handscube.
 * @author J.W. <email@email.com>
 */

class Event extends BaseEvent
{

    public function __construct()
    {
        parent::__construct(func_get_args());
        $this->name = self::getIndex();
    }

    /**
     * Sets the Trigger that fires the event.
     *
     * @param Object $trigger
     * @return void
     */
    public function from(Object $trigger)
    {
        $this->trigger = (string) $trigger;
    }

    /**
     * Get event store index. [reserved]
     *
     * @return void
     */
    public static function getIndex()
    {
        $className = substr(static::class, strrpos(static::class, '\\') + 1);
        $splits = preg_split("/(?=[A-Z])/", lcfirst($className));
        $index = strtolower(implode('-', $splits));
        return $index;
    }

    /**
     * Trigger an event.
     *
     * @param BaseEvent $event
     * @param [Object] $trigger
     * @return void
     */
    public static function emit(BaseEvent $event, $trigger)
    {
        $event->trigger = get_class($trigger);
        (new EventSchedule())->handle($event);
    }

    /**
     * The alias of @method emit.
     *
     * @param BaseEvent $event
     * @param [type] $trigger
     * @return void
     */
    public static function trigger(BaseEvent $event, $trigger)
    {
        self::emit($event, $trigger);
    }

    /**
     * Listen event.
     *
     * @param [string | object] $event
     * @param [string | array ] $listeners
     * @return boolean suceess return ture else return false.
     */
    public static function listen($event, $listeners)
    {
        if (is_object($event)) {
            $event = get_class($event);
        }
        if (strpos($event, '\\') === 0) {
            $event = substr($event, 1);
        }
        if (!isset(EventSchedule::$listeners[$event])) {
            EventSchedule::$listeners[$event] = (array) $listeners;
            return true;
        } else {
            EventSchedule::$listeners[$event] = array_merge((array) EventSchedule::$listeners[$event], (array) $listeners);
        }
        return false;
    }

    /**
     * The alias of @method listen.
     *
     * @param [type] $event
     * @param string $listeners
     * @return void
     */
    public static function on($event, string $listeners)
    {
        return self::listen($event, $listeners);
    }

    /**
     * Return the complete event data.
     *
     * @return array $data
     */
    public function getData()
    {
        return $this->container;
    }
}
