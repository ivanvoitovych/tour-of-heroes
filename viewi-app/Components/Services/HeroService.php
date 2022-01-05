<?php

namespace Components\Services;

use Components\Mocks\HerosMocks;
use Components\Models\HeroModel;

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

    public function GetHero(int $id): ?HeroModel
    {
        $this->messageService->Add("HeroService: fetched hero id={$id}");
        $searchResult = array_values(array_filter(
            $this->herosMocks->GetHeroes(),
            function (HeroModel $x) use ($id) {
                return $x->Id == $id;
            }
        ));
        return $searchResult ? $searchResult[0] : null;
    }
}
