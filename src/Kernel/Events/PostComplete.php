<?php

namespace Handscube\Kernel\Events;

use App\Models\Post;

class PostComplete extends Event
{

    public $post;

    public function __construct(Post $post)
    {
        parent::__construct($post);
        $this->post = $post;
    }
}
