<?php

namespace Components\Views\Heroes;

use Components\Mocks\HerosMocks;
use Components\Models\HeroModel;
use Viewi\BaseComponent;

class Heroes extends BaseComponent
{
    /**
     * 
     * @var HeroModel[]
     */
    public array $heros;
    public ?HeroModel $selectedHero = null;

    public function __construct(HerosMocks $herosMocks)
    {
        $this->heros = $herosMocks->GetHeroes();
    }

    public function onSelect(HeroModel $hero)
    {
        $this->selectedHero = $hero;
    }
}
