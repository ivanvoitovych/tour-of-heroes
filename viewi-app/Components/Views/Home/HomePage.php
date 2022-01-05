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
    public array $heros;

    public function __init(HeroService $heroService)
    {
        $this->heros = array_slice($heroService->GetHeroes(), 0, 4);
    }
}
