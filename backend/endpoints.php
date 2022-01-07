<?php

namespace BackendApp;

use Components\Models\HeroModel;
use Viewi\Common\JsonMapper;
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

Router::register('post', '/api/heroes', function () {
    // read the data
    $inputContent = file_get_contents('php://input');
    // parse
    $stdObject = json_decode($inputContent, false);
    // convert type
    $hero = JsonMapper::Instantiate(HeroModel::class, $stdObject);
    $repository = new Repository(HeroModel::class);
    return $repository->Create($hero);
});

Router::register('put', '/api/heroes/{id}', function () {
    // read the data
    $inputContent = file_get_contents('php://input');
    // parse
    $stdObject = json_decode($inputContent, false);
    // convert type
    $hero = JsonMapper::Instantiate(HeroModel::class, $stdObject);
    $repository = new Repository(HeroModel::class);
    return $repository->Update($hero);
});

Router::register('delete', '/api/heroes/{id}', function (int $id) {
    $repository = new Repository(HeroModel::class);
    return $repository->Delete($id);
});

// 404 
Router::register('*', '/api/*', function () {
    return Response::Json([
        "message" => "Not Found"
    ])->WithCode(404);
});