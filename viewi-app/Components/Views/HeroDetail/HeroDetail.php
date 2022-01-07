<?php

namespace Components\Views\HeroDetail;

use Components\Models\HeroModel;
use Components\Services\HeroService;
use Viewi\BaseComponent;
use Viewi\Common\ClientRouter;

class HeroDetail extends BaseComponent
{
    public ?HeroModel $hero = null;
    private ClientRouter $router;
    private HeroService $heroService;

    public function __init(HeroService $heroService, ClientRouter $router, int $id)
    {
        $this->heroService = $heroService;
        $heroService->GetHero($id, function (?HeroModel $hero) {
            $this->hero = $hero;
        });
        $this->router = $router;
    }

    public function GoBack()
    {
        $this->router->navigateBack();
    }

    public function Save()
    {
        $this->heroService->Update($this->hero, function () {
            $this->router->navigateBack();
        });
    }
}
