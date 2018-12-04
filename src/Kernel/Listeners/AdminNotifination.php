<?php

namespace Handscube\Kernel\Listeners;

use Handscube\Kernel\Events\Event;

class AdminNotifination extends Listener
{

    public function handle(Event $event)
    {
        // ff("Listener admin handle.Event $event->name done here. Post " . $event->post->id . 'complete');
        echo "Admin Listner trigger.\n";
    }
}
