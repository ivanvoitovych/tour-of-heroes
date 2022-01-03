<?php

namespace Components\Views\Messages;

use Components\Services\MessageService;
use Viewi\BaseComponent;

class Messages extends BaseComponent
{
    public MessageService $messageService;

    public function __init(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }
}
