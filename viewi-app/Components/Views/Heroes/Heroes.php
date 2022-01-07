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

    public function __init(HeroService $heroService, MessageService $messageService)
    {
        $heroService->GetHeroes(function (array $heroes) {
            $this->heros = $heroes;
        });
        $this->messageService = $messageService;
    }
}
