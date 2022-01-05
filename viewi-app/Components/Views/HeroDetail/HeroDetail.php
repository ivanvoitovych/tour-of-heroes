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

    public function __init(HeroService $heroService, ClientRouter $router, int $id)
    {
        $this->hero = $heroService->GetHero($id);
        $this->router = $router;
    }

    public function GoBack()
    {
        $this->router->navigateBack();
    }
}
