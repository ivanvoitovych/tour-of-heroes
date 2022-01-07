<?php

namespace BackendApp;

use Components\Models\HeroModel;
use Viewi\Routing\Router;
use Viewi\WebComponents\Response;

include 'repository.php';

// API
Router::register('get', '/api/heroes', function () {
    $repository = new Repository(HeroModel::class);
    return $repository->Get();
});

Router::register('get', '/api/heroes/{id}', function (int $id) {
    $repository = new Repository(HeroModel::class);
    return $repository->GetById($id);
});

// 404 
Router::register('*', '/api/*', function () {
    return Response::Json([
        "message" => "Not Found"
    ])->WithCode(404);
});