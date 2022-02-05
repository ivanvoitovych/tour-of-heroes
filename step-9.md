# Step 9 - Get data from a server

This section will learn how to use HttpClient to communicate with the server.

Let's create an API endpoint to serve the data for us. Viewi can be used as a standalone application to make API endpoints without installing other libraries or frameworks. That helps you develop the application while your backend is still under development.

## Prepare backend folder

Please create a `backend` folder and copy repository file and JSON with data file from here:

[/backend/HeroModel.json](https://raw.githubusercontent.com/ivanvoitovych/tour-of-heroes/master/backend/HeroModel.json)

[/backend/repository.php](https://raw.githubusercontent.com/ivanvoitovych/tour-of-heroes/master/backend/repository.php)

In real life, you can use whatever source of data you want. But for this example, let's use a dummy JSON file.

Now let's create our backend endpoints using `Viewi\Routing\Router`. For that, let's make a file:

`backend\endpoints.php`

```php
<?php

namespace BackendApp;
```

And include it in our index.php before Viewi routes.

`public\index.php`

```php
<?php

require __DIR__ . '/../vendor/autoload.php';

// include backend
include __DIR__ . '/../backend/endpoints.php';

// Viewi application here
include __DIR__ . '/../viewi-app/viewi.php';
Viewi\App::handle();
```

Now we are ready to create API endpoints. Let's do this. First, let's add a 404 route for all the `/api/*` requests:

```php
// 404 
Router::register('*', '/api/*', function () {
    return Response::Json([
        "message" => "Not Found"
    ])->WithCode(404);
});
```

Here we use the `Router::register` method with arguments:

 `$method = '*'` which means all the methods

 `$url = '/api/*'` which will be processed on all requests that start with `/api/` in their base path

 `$actionOrController = function..` we attached a callback function which Viewi will execute once the route matches the pattern URL.

 `Viewi\WebComponents\Response` is a helper class used to format the response, put some headers, etc.

 Remember, routes with `*` (like '/api/*') should be declared at the end to prevent interception for the rest of the endpoint rules.

## Let's create a `/api/heroes` endpoint which will return the data from our dummy data source:

```php
use Components\Models\HeroModel;
//...
Router::register('get', '/api/heroes', function () {
    $repository = new Repository(HeroModel::class);
    return $repository->Get();
});
```

When the base path is `/api/heroes`, the application will return data from the repository with the `HeroModel` type. In a real-world application, you most likely will need to map your DB entity object(s) to model(s) from Viewi.
Everything that is returned from the callback function will be produced as an HTTP response and automatically converted into the JSON content type if necessary.

Add another one:

```php
Router::register('get', '/api/heroes/{id}', function (int $id) {
    $repository = new Repository(HeroModel::class);
    return $repository->GetById($id);
});
```

Here we use the `/api/heroes/{id}` route match rule, which will capture `id` and pass it to the callback function as an argument with the same name.

The final version of the file:

`backend\endpoints.php`

```php
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
```

## Calling an API

Now we are ready to consume our API. Let's modify our `HeroService` to use `HttpClient` instead of mocks.

```php
private HttpClient $http;
private MessageService $messageService;

public function __construct(HttpClient $http, MessageService $messageService)
{
    $this->http = $http;
    $this->messageService = $messageService;
}
```

To make a request let's use `http->get` method. It accepts two parameters.
The first is a callback function for successful response processing, which has response data.
And the second one is optional and will be executed if some error has occurred.

```php
$this->http->get('/api/heroes')->then(function (array $heroes) {
        // use $heroes
    }, function ($error) {
        // error
    });
```

Let's modify `GetHeroes` and `GetHero` methods. 
Since the request is an asynchronous operation, we will need to accept a callback instead of returning a value:

```php
public function GetHeroes(callable $callback)
{
    $this->http->get('/api/heroes')->then(function (array $heroes) use ($callback) {
        $callback($heroes);
    }, function ($error) {
        // error
    });
}
```

Let's add some messages with `messageService->Add`, and the final version should be like this:

`viewi-app\Components\Services\HeroService.php`

```php
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
}
```

Now we need to modify our components to use `HeroService` properly:

By changing this:

`$this->hero = $heroService->GetHero($id);` 

to this

```php
$heroService->GetHero($id, function (?HeroModel $hero) {
    $this->hero = $hero;
});
```

This:

`$this->heroes = array_slice($heroService->GetHeroes(), 0, 4);`

to this:

```php
$heroService->GetHeroes(function (array $heroes) {
    $this->heroes = array_slice($heroes, 0, 4);
});
```

And this:

`$this->heroes = $heroService->GetHeroes();`

to this:

```php
$heroService->GetHeroes(function (array $heroes) {
    $this->heroes = $heroes;
});
```

Also, `viewi-app\Components\Views\HeroDetail\HeroDetail.html` will need to be fixed a little. 
Since the $hero property can be null now, we need to change this:

`<Layout title="{$hero->Name} details">`

to this:

`<Layout title="{$hero ? $hero->Name : ''} details">`

When you refresh the page, your application will load the data from the server. Also, if you inspect the network tab, you can see fully rendered HTML content with the data. That's SSR out of the box. 

If you click some pages, new data will be requested using AJAX.

## [Step 10 - Saving hero changes](/step-10.md)

## [Home](/README.md#Steps)