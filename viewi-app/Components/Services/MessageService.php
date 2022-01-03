<?php

namespace Components\Services;

class MessageService
{
    /**
     * 
     * @var string[]
     */
    public array $messages = [];   

    public function Add(string $message)
    {
        $this->messages[] = $message;
    }

    public function Clear()
    {
        $this->messages = [];
    }
}