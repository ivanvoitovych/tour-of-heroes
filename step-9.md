# Step 9 - Get data from a server

In this section we will learn to user HttpClient in order to communicate with the server.

Let's create an API endpoint to serve the data for us. Viewi can be used as a standalone application where you can create API endpoints without installing any other libraries or frameworks. That helps you to develop the application while your backend is still under development.

## Prepare backend folder

Please create a `backend` folder and put there file repository file and a json data file. In real life you can use whatever source of data you want. But for this example let's use some dummy json file.

`backend\HeroModel.json`

```json
[
    {
        "Id": 1,
        "Name": "Metal Man"
    },
    {
        "Id": 2,
        "Name": "Firefly"
    },
    {
        "Id": 3,
        "Name": "Mastermind"
    },
    {
        "Id": 4,
        "Name": "Bulletproof"
    },
    {
        "Id": 5,
        "Name": "Fireball"
    },
    {
        "Id": 6,
        "Name": "Apex"
    },
    {
        "Id": 7,
        "Name": "Turbine"
    },
    {
        "Id": 8,
        "Name": "Tarantula"
    },
    {
        "Id": 9,
        "Name": "Shockwave"
    },
    {
        "Id": 10,
        "Name": "Steamroller"
    }
]
```

`backend\repository.php`

```php
<?php

namespace BackendApp;

use Components\Models\HeroModel;
use ReflectionException;
use Viewi\Common\JsonMapper;

class Repository
{
    /**
     * 
     * @var null|HasId[]
     */
    private ?array $data = null;
    private bool $ready = false;
    private string $className;
    private string $fileName;

    public function __construct(string $className)
    {
        $this->className = $className;
        $baseName = substr(strrchr($className, "\\"), 1);
        $this->fileName = __DIR__ . "/$baseName.json";
    }

    public function Get(): array
    {
        $this->Prepare();
        return $this->data;
    }

    public function GetById(int $id)
    {
        $this->Prepare();
        $searchResult = array_values(array_filter(
            $this->data,
            function ($x) use ($id) {
                /** @var HasId $x */
                return $x->Id == $id;
            }
        ));
        return $searchResult ? $searchResult[0] : null;
    }

    /**
     * 
     * @param int $id 
     * @param HasId $object 
     * @return bool 
     * @throws ReflectionException 
     */
    public function Update($object)
    {
        $this->Prepare();
        foreach ($this->data as $index => $item) {
            if ($item->Id === $object->Id) {
                $this->data[$index] = $object;
                $this->Flush();
                return true;
            }
        }
        return false;
    }

    /**
     * 
     * @param HasId $object 
     * @return bool 
     * @throws ReflectionException 
     */
    public function Create($object)
    {
        $this->Prepare();
        if (count($this->data)) {
            $object->Id = $this->data[count($this->data) - 1]->Id + 1;
        } else {
            $object->Id = 1;
        }
        $this->data[] = $object;
        $this->Flush();
        return $object;
    }

    public function Delete(int $id)
    {
        $this->Prepare();
        foreach ($this->data as $index => $item) {
            if ($item->Id === $id) {
                unset($this->data[$index]);
                $this->data = array_values($this->data);
                $this->Flush();
                return true;
            }
        }
        return false;
    }

    private function Flush()
    {
        file_put_contents($this->fileName, json_encode($this->data));
    }

    private function Prepare()
    {
        if (!$this->ready) {
            $this->data = [];
            if (file_exists($this->fileName)) {
                $json = file_get_contents($this->fileName);
                $objects = json_decode($json, false);
                foreach ($objects as $object) {
                    $this->data[] = JsonMapper::Instantiate($this->className, $object);
                }
            } else {
                $this->data = [];
                file_put_contents($this->fileName, json_encode($this->data));
            }
        }
    }
}

abstract class HasId
{
    public int $Id;
}
```

Now let's create our backend endpoints using `Viewi\Routing\Router`. For that let's create a file:

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

Now we are ready to create API endpoints. Let's do this. First le'ts add a 404 route for all the `/api/*` requests:

```php
// 404 
Router::register('*', '/api/*', function () {
    return Response::Json([
        "message" => "Not Found"
    ])->WithCode(404);
});
```

Here we use `Router::register` method with arguments:

 `$method = '*'` which means all the methods

 `$url = '/api/*'` which will be processed on all requests that start with `/api/` in their base path

 `$actionOrController = function..` we attached a callback function which will be executed once the route will match the pattern url.

 `Viewi\WebComponents\Response` is a helper class that is used to format the response, put some headers, etc.

 Remember, routes with `*` (like '/api/*') should be declared at the end to prevent interception for the rest of endpoint rules.

## Let's create `/api/heroes` endpoint which will return the data from our dummy data source:

```php
use Components\Models\HeroModel;
//...
Router::register('get', '/api/heroes', function () {
    $repository = new Repository(HeroModel::class);
    return $repository->Get();
});
```

Now, when the base path is `/api/heroes`, the application will return data from the repository with `HeroModel` type.
Everything that is returned from the callback function will be returned as a http response and automatically converted into the json content type if necessary.

And another one:

```php
Router::register('get', '/api/heroes/{id}', function (int $id) {
    $repository = new Repository(HeroModel::class);
    return $repository->GetById($id);
});
```

Here we use `/api/heroes/{id}` route match rule which will capture `id` and pass it to the callback function as an argument with the same name.

The final version of file:

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

Now we are ready to consume our API. Let's modify our HeroService to use HttpClient instead of using mocks.

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
First one is a callback function for the successful response processing which has a response data.
And the second one is optional and will be executed if some error has occurred.

```php
$this->http->get('/api/heroes')->then(function (array $heroes) {
        // use $heroes
    }, function ($error) {
        // error
    });
```

Let's modify GetHeroes and GetHero methods. 
Since the request is an asynchronous operation we will need to accept a callback instead of returning a value:

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

Let's add some messages with `messageService->Add` and the final version will be like this:

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

Now we need to modify our components to properly use HeroService:

By changing this:

`$this->hero = $heroService->GetHero($id);` 

to this

```php
$heroService->GetHero($id, function (?HeroModel $hero) {
    $this->hero = $hero;
});
```

This:

`$this->heros = array_slice($heroService->GetHeroes(), 0, 4);`

to this:

```php
$heroService->GetHeroes(function (array $heroes) {
    $this->heros = array_slice($heroes, 0, 4);
});
```

And this:

`$this->heros = $heroService->GetHeroes();`

to this:

```php
$heroService->GetHeroes(function (array $heroes) {
    $this->heros = $heroes;
});
```

Also `viewi-app\Components\Views\HeroDetail\HeroDetail.html` will need to be fixed a little. 
Since the $hero property can be null now we need to change this:

`<Layout title="{$hero->Name} details">`

to this:

`<Layout title="{$hero ? $hero->Name : ''} details">`

Now when you refresh the page your application will load the data from the server.
Also you may notice that the html page from the server will come already pre rendered with the data (SSR).
Only if you click some pages then the fresh data will be requested using AJAX.

## [Step 10 - Saving hero changes](/step-10.md)

## [Home](/README.md#Steps)