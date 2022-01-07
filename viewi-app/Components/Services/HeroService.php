<?php

namespace Components\Services;

use Components\Models\HeroModel;
use Viewi\Common\HttpClient;

class HeroService
{
    private HttpClient $http;
    private MessageService $messageService;

    public function __construct(HttpClient $http, MessageService $messageService)
    {
        $this->http = $http;
        $this->messageService = $messageService;
    }

    public function GetHeroes(callable $callback)
    {
        $this->messageService->Add('HeroService: fetching heroes');
        $this->http->get('/api/heroes')->then(function (array $heroes) use ($callback) {
            $this->messageService->Add('HeroService: fetched heroes');
            $callback($heroes);
        }, function ($error) {
            $this->messageService->Add('HeroService: error has occurred. ' . json_encode($error));
        });
    }

    public function GetHero(int $id, callable $callback)
    {
        $this->messageService->Add("HeroService: fetching hero id={$id}");
        $this->http->get("/api/heroes/{$id}")->then(function (?HeroModel $hero) use ($id, $callback) {
            $this->messageService->Add("HeroService: fetched hero id={$id}");
            $callback($hero);
        }, function ($error) {
            $this->messageService->Add('HeroService: error has occurred. ' . json_encode($error));
        });
    }

    public function Update(HeroModel $hero, callable $callback)
    {
        $this->messageService->Add("HeroService: updating hero id={$hero->Id}");
        $this->http->put("/api/heroes/{$hero->Id}", $hero)->then(function () use ($hero, $callback) {
            $this->messageService->Add("HeroService: updated hero id={$hero->Id}");
            $callback();
        }, function ($error) {
            $this->messageService->Add('HeroService: error has occurred. ' . json_encode($error));
        });
    }

    public function Create(HeroModel $hero, callable $callback)
    {
        $this->messageService->Add("HeroService: creating hero");
        $this->http->post("/api/heroes", $hero)->then(function (HeroModel $newHero) use ($callback) {
            $this->messageService->Add("HeroService: created hero id={$newHero->Id}");
            $callback($newHero);
        }, function ($error) {
            $this->messageService->Add('HeroService: error has occurred. ' . json_encode($error));
        });
    }

    public function Delete(int $id, callable $callback)
    {
        $this->messageService->Add("HeroService: deleting hero $id");
        $this->http->delete("/api/heroes/$id")->then(function () use ($id, $callback) {
            $this->messageService->Add("HeroService: deleted hero id={$id}");
            $callback();
        }, function ($error) {
            $this->messageService->Add('HeroService: error has occurred. ' . json_encode($error));
        });
    }
}
