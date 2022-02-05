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
    public array $heroes;
    public string $heroName = '';
    private HeroService $heroService;

    public function __init(HeroService $heroService, MessageService $messageService)
    {
        $this->heroService = $heroService;
        $this->messageService = $messageService;
        $this->ReadHeroes();
    }

    public function ReadHeroes()
    {
        $this->heroService->GetHeroes(function (array $heroes) {
            $this->heroes = $heroes;
        });
    }

    public function Add()
    {
        if (strlen($this->heroName)) {
            $hero = new HeroModel();
            $hero->Name = $this->heroName;
            $this->heroService->Create($hero, function () {
                $this->heroName = '';
                $this->ReadHeroes();
            });
        }
    }

    public function Delete(HeroModel $hero)
    {
        $this->heroService->Delete($hero->Id, function () {
            $this->ReadHeroes();
        });
    }
}
