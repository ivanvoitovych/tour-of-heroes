<?php

namespace Components\Services;

use Components\Mocks\HerosMocks;

class HeroService
{
    private HerosMocks $herosMocks;
    private MessageService $messageService;

    public function __construct(HerosMocks $herosMocks, MessageService $messageService)
    {
        $this->herosMocks = $herosMocks;
        $this->messageService = $messageService;
    }

    public function GetHeroes(): array
    {
        $this->messageService->Add('HeroService: fetched heroes');
        return $this->herosMocks->GetHeroes();
    }
}
