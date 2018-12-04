<?php

namespace Handscube\Kernel\Schedules;

use Handscube\Kernel\Events\Event;
use Handscube\Kernel\Exceptions\NotFoundException;
use Handscube\Kernel\Schedules\Schedule;

/**
 * Class Event SChedule. [c] Handsucbe.
 *
 * @author J.W. <email@email.com>
 */

class EventSchedule extends Schedule
{
    public static $listeners = [];
    public static $subscribers = [];
    public static $observers = [];

    /**
     * Initalize.
     *
     * @param array $listeners
     * @param array $subscribers
     * @param array $observers
     */
    public function __construct(array $listeners = [], array $subscribers = [], array $observers = [])
    {
        self::$listeners = self::$listeners ?: $listeners;
        self::$subscribers = self::$subscribers ?: $subscribers;
        self::$observers = self::$observers ?: $observers;
    }

    /**
     * Hand events.
     *
     * @param Event $event
     * @return void
     */
    public function handle(Event $event = null)
    {
        $this->parseSubscribersToListeners();
        $this->dispatch($event);
    }

    /**
     * Dispatch event to receptor.
     * Like dispatch queue server or other backend server. [reserved]
     * @param Event $event
     * @return void
     */
    public function dispatch(Event $event)
    {
        $this->notify($event);
        // $index = Event::getIndex($event);
    }

    /**
     * Notify obersvies that bind this event.
     *
     * @param [type] $event
     * @return void
     */
    public function notify($event)
    {
        foreach (self::$listeners as $idx => $listener) {
            if (get_class($event) === $idx) {
                foreach ($listener as $listenerItem) {
                    if (strpos($listenerItem, '@') === false) {
                        $resLis = $this->notifyListener($listenerItem, $event);
                        if ($resLis === false) {
                            return;
                        }
                    } else {
                        $resSub = $this->notifySubscriber($listenerItem, $event);
                        if ($resSub === false) {
                            return;
                        }
                    }
                }
            }
        }
    }

    /**
     * Notify listeners.
     *
     * @param [type] $listeners
     * @return void
     */
    public function notifyListener($listener, $event)
    {
        return (new $listener)->handle($event);
    }

    public function notifySubscriber($listener, $event)
    {
        $split = explode('@', $listener);
        $subscriber = $split[0];
        $method = $split[1];
        if (method_exists($subscriber, $method)) {
            return (new $subscriber)->$method($event);
        } else {
            throw new NotFoundException("Method $method does not exists in subscriber $subscriber.");
        }
    }

    /**
     * Exchange subsribes to listeners.
     *
     * @return void
     */
    public function parseSubscribersToListeners()
    {
        foreach (self::$subscribers as $subscriber) {
            (new $subscriber)->subscribe((new Event()));
        }
    }
}
