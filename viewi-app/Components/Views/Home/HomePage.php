<?php

namespace Components\Views\Home;

use Components\Services\HeroService;
use Viewi\BaseComponent;

class HomePage extends BaseComponent
{
    /**
     * 
     * @var HeroModel[]
     */
    public array $heros = [];

    public function __init(HeroService $heroService)
    {
        $heroService->GetHeroes(function (array $heroes) {
            $this->heros = array_slice($heroes, 0, 4);
        });
    }
}
