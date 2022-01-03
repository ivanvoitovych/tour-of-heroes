<?php

namespace Components\Views\Heroes;

use Components\Models\HeroModel;
use Components\Services\HeroService;
use Components\Services\MessageService;
use Viewi\BaseComponent;

class Heroes extends BaseComponent
{
    /**
     * 
     * @var HeroModel[]
     */
    public array $heros;
    public ?HeroModel $selectedHero = null;
    private MessageService $messageService;

    public function __construct(HeroService $heroService, MessageService $messageService)
    {
        $this->heros = $heroService->GetHeroes();
        $this->messageService = $messageService;
    }

    public function onSelect(HeroModel $hero)
    {
        $this->selectedHero = $hero;
        $this->messageService->Add("Heroes component: Selected hero id={$hero->Id}");
    }
}
