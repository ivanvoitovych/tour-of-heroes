# Step 8 - Add Navigation and Routes

Now it's time to break our application into pages. And assign a route to each of them.

Let's remove `Heroes` and `Messages` components from our home page.

`viewi-app\Components\Views\Home\HomePage.html`

```html
<Layout title="$title">
    <h1>$title</h1>
</Layout>
```

Then we add the `Messages` component to the Layout. And change the title's base from `Viewi` to `Tour of Heroes`:

`viewi-app\Components\Views\Layouts\Layout.html`

```html
<!DOCTYPE html>
<html lang="en">

<head>
    <title>
        $title | Tour of Heroes
    </title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <CssBundle link="/css/main.css" />
</head>

<body>
    <div id="content">
        <h1>Tour of Heroes</h1>
        <slot></slot>
    </div>
    <Messages></Messages>
    <ViewiScripts />
</body>

</html>
```

Now let's modify our `HomePage` component to display TOP 4 heroes. Let's call it a Dashboard.

`viewi-app\Components\Views\Home\HomePage.php`

```php
<?php

namespace Components\Views\Home;

use Components\Services\HeroService;
use Viewi\BaseComponent;

class HomePage extends BaseComponent
{
    /**
     * 
     * @var HeroModel[]
     */
    public array $heroes;

    public function __init(HeroService $heroService)
    {
        $this->heroes = array_slice($heroService->GetHeroes(), 0, 4);
    }
}
```

`viewi-app\Components\Views\Home\HomePage.html`

```html
<Layout title="Dashboard">
    <h2>Top Heroes</h2>
    <div class="heroes-menu">
        <a foreach="$heroes as $hero" href="/detail/{$hero->Id}">
            {$hero->Name}
        </a>
    </div>
</Layout>
```

You should see the Dashboard with the top 4 heroes when you refresh the page. But when you click on the Hero, you will see a `page not found message. Let's fix it.

First, we need to set up a route for our HeroDetail component.

`ViewiRoute::get('/detail/{id}', HeroDetail::class);`

`viewi-app\routes.php`

```php
<?php

use Components\Views\HeroDetail\HeroDetail;
use Components\Views\Home\HomePage;
use Components\Views\NotFound\NotFoundPage;
use Viewi\Routing\Route as ViewiRoute;

ViewiRoute::get('/', HomePage::class);
ViewiRoute::get('/detail/{id}', HeroDetail::class);
ViewiRoute::get('*', NotFoundPage::class);
```

More about routing here: [https://viewi.net/docs/routing](https://viewi.net/docs/routing)

We need to receive that id in our HeroDetail component and get the Hero. Route params will get injected automatically into your component.

`viewi-app\Components\Views\HeroDetail\HeroDetail.php`

```php
<?php

namespace Components\Views\HeroDetail;

use Components\Models\HeroModel;
use Viewi\BaseComponent;

class HeroDetail extends BaseComponent
{
    public ?HeroModel $hero = null;

    public function __init(int $id)
    {
        // get the Hero by $id
    }
}
```

Now let's make a good view for our page by wrapping it around the `Layout` component and passing the title with the Hero's name:

`viewi-app\Components\Views\HeroDetail\HeroDetail.html`

```html
<Layout title="{$hero->Name} details">
    <div if="$hero">
        <h2>{strtoupper($hero->Name)} Details</h2>
        <div><span>id: </span>{$hero->Id}</div>
        <div>
            <label for="hero-name">Hero name: </label>
            <input id="hero-name" model="$hero->Name" placeholder="name">
        </div>
    </div>
</Layout>
```

Now we need to get our Hero. For that, let's add a new method `GetHero(int $id)` in our `HeroService` and use it inside our HeroDetail component:

`viewi-app\Components\Services\HeroService.php`

```php
<?php

namespace Components\Services;

use Components\Mocks\HeroesMocks;
use Components\Models\HeroModel;

class HeroService
{
    private HeroesMocks $heroesMocks;
    private MessageService $messageService;

    public function __construct(HeroesMocks $heroesMocks, MessageService $messageService)
    {
        $this->heroesMocks = $heroesMocks;
        $this->messageService = $messageService;
    }

    public function GetHeroes(): array
    {
        $this->messageService->Add('HeroService: fetched heroes');
        return $this->heroesMocks->GetHeroes();
    }

    public function GetHero(int $id): ?HeroModel
    {
        $this->messageService->Add("HeroService: fetched hero id={$id}");
        $searchResult = array_values(array_filter(
            $this->heroesMocks->GetHeroes(),
            function (HeroModel $x) use ($id) {
                return $x->Id == $id;
            }
        ));
        return $searchResult ? $searchResult[0] : null;
    }
}
```

`viewi-app\Components\Views\HeroDetail\HeroDetail.php`

```php
<?php

namespace Components\Views\HeroDetail;

use Components\Models\HeroModel;
use Components\Services\HeroService;
use Viewi\BaseComponent;

class HeroDetail extends BaseComponent
{
    public ?HeroModel $hero = null;

    public function __init(HeroService $heroService, int $id)
    {
        $this->hero = $heroService->GetHero($id);
    }
}
```

When you refresh the page and click on the Hero, you should see the details page. Also, when you type the name, it will update it on the page, including the page's title.

## Let's make the same for our `Heroes` component:

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
    public array $heroes;

    public function __init(HeroService $heroService, MessageService $messageService)
    {
        $this->heroes = $heroService->GetHeroes();
        $this->messageService = $messageService;
    }
}
```

`viewi-app\Components\Views\Heroes\Heroes.html`

```html
<Layout title="My Heroes">
    <h2>My Heroes</h2>
    <ul class="heroes">
        <li foreach="$heroes as $hero">
            <a href="/detail/{$hero->Id}"><span class="badge">{$hero->Id}</span> {$hero->Name}</a>
        </li>
    </ul>
</Layout>
```

Add a route: `ViewiRoute::get('/heroes', Heroes::class);`

`viewi-app\routes.php`

```php
<?php

use Components\Views\HeroDetail\HeroDetail;
use Components\Views\Heroes\Heroes;
use Components\Views\Home\HomePage;
use Components\Views\NotFound\NotFoundPage;
use Viewi\Routing\Route as ViewiRoute;

ViewiRoute::get('/', HomePage::class);
ViewiRoute::get('/heroes', Heroes::class);
ViewiRoute::get('/detail/{id}', HeroDetail::class);
ViewiRoute::get('*', NotFoundPage::class);
```

Ok, cool, but how about some menu navigation. Let's create it in our Layout component:

`viewi-app\Components\Views\Layouts\Layout.html`

```html
<!DOCTYPE html>
<html lang="en">

<head>
    <title>
        $title | Tour of Heroes
    </title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <CssBundle link="/css/main.css" />
</head>

<body>
    <div id="content">
        <h1>Tour of Heroes</h1>
        <nav>
            <a href="/">Dashboard</a>
            <a href="/heroes">Heroes</a>
        </nav>
        <slot></slot>
    </div>
    <Messages></Messages>
    <ViewiScripts />
</body>

</html>
```

Now we can navigate through pages. The only issue is that we can't navigate back from the hero details page. Let's fix it with `ClientRouter` and the `go back` button:

More about `ClientRouter` here: [https://viewi.net/docs/client-router](https://viewi.net/docs/client-router)

You need to call the `navigateBack` method in `ClientRouter` to return.

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
```

`viewi-app\Components\Views\HeroDetail\HeroDetail.html`

```html
<Layout title="{$hero->Name} details">
    <div if="$hero">
        <h2>{strtoupper($hero->Name)} Details</h2>
        <div><span>id: </span>{$hero->Id}</div>
        <div>
            <label for="hero-name">Hero name: </label>
            <input id="hero-name" model="$hero->Name" placeholder="name">
        </div>
        <button (click)="GoBack()">go back</button>
    </div>
</Layout>
```

And that's it. Now you can navigate through pages and edit Heroes.

## [Step 9- Get data from a server](/step-9.md)

## [Home](/README.md#Steps)