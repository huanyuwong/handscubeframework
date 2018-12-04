<?php

namespace Handscube\Kernel\Listeners;

use Handscube\Kernel\Events\Event;

class Subscriber
{

    protected $name;

    public function __construct(string $name = '', array $events = [])
    {

    }

    public function subscribe(Event $events)
    {

    }

    public function getName()
    {
        return $this->name ?: '';
    }
}
