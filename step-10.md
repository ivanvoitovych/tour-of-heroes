# Step 10 - Saving hero changes

What about saving a hero. Let's add an API endpoint to handle that:

```php
Router::register('put', '/api/heroes/{id}', function (int $id) {
    // read the data
    $inputContent = file_get_contents('php://input');
    // parse
    $stdObject = json_decode($inputContent, false);
    // convert type
    $hero = JsonMapper::Instantiate(HeroModel::class, $stdObject);
    $repository = new Repository(HeroModel::class);
    return $repository->Update($id, $hero);
});
```

Here we read the request body, encode it into the object.
And then convert it to the HeroModel using `Viewi\Common\JsonMapper`.

Now let's add a save button and a handler in our HeroDetail component:

`viewi-app\Components\Views\HeroDetail\HeroDetail.html`

```html
<Layout title="{$hero ? $hero->Name : ''} details">
    <div if="$hero">
        <h2>{strtoupper($hero->Name)} Details</h2>
        <div><span>id: </span>{$hero->Id}</div>
        <div>
            <label for="hero-name">Hero name: </label>
            <input id="hero-name" model="$hero->Name" placeholder="name">
        </div>
        <button (click)="GoBack()">Back</button>
        <button (click)="Save()">Save</button>
    </div>
</Layout>
```

`viewi-app\Components\Views\HeroDetail\HeroDetail.php`

```php
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
```

Now we need to add Update method to our HeroService.

```php
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
```

Now we should be able to save the changes on a server.

## Create and Delete

By following the same pattern, let's update our application with Create and Delete functionality:


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
```

`backend\endpoints.php`

```php
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
```

`viewi-app\Components\Views\Heroes\Heroes.php`

```php
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
    public array $heros;
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
            $this->heros = $heroes;
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
```

`viewi-app\Components\Views\Heroes\Heroes.html`

```html
<Layout title="My Heroes">
    <h2>My Heroes</h2>
    <div>
        <label for="new-hero">Hero name: </label>
        <input id="new-hero" model="$heroName" />
        <button class="add-button" (click)="Add()">
            Add hero
        </button>
    </div>
    <ul class="heroes">
        <li foreach="$heros as $hero">
            <a href="/detail/{$hero->Id}"><span class="badge">{$hero->Id}</span> {$hero->Name}</a>
            <button class="delete" title="delete hero" (click)="Delete($hero)">x</button>
        </li>
    </ul>
</Layout>
```

After refreshing the page you should be able to read, update, create and delete heroes.

I hope you liked this tutorial. And I apologize if something is unclear, I'm not much of a tutor.

Feel free to contact me if you have any questions or found a bug.

## [Home](/README.md#Steps)