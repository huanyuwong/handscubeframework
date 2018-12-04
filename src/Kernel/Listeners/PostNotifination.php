<?php

namespace Handscube\Kernel\Listeners;

use Handscube\Kernel\Events\Event;

class PostNotifination extends Listener
{
    public function handle(Event $event)
    {
        echo "PostNotfination listener trigger.\n";
    }
}
