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
    public array $heroes = [];

    public function __init(HeroService $heroService)
    {
        $heroService->GetHeroes(function (array $heroes) {
            $this->heroes = array_slice($heroes, 0, 4);
        });
    }
}
