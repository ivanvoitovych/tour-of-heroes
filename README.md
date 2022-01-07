# Tour of Heroes app and tutorial

-----------
Borrowed from Angular [Tour of Heroes](https://angular.io/tutorial)

If you see any copyright violations please mail me and I will take actions immediately.

# Step 1

Create a folder for your project.

Inside of this folder create our `public` folder with `index.php` in it:

```
<?php

require __DIR__ . '/../vendor/autoload.php';
```

# Step 2

run the following commands: 

`composer require viewi/viewi`

`vendor/bin/viewi new`

Now you should be able to run your application:

`cd public`

`php -S localhost:8000`

# Step 3

Let's change the title on our home page:

`viewi-app\Components\Views\Home\HomePage.php`

`public string $title = 'Tour of Heroes';`

# Step 4 - The Hero Editor

## Let's create the heroes component:

`viewi-app\Components\Views\Heroes\Heroes.php`

```php
<?php

namespace Components\Views\Heroes;

use Viewi\BaseComponent;

class Heroes extends BaseComponent
{
    public string $hero = 'Mastermind';
}
```

`viewi-app\Components\Views\Heroes\Heroes.html`

```html
<h2>$hero</h2>
```

## Show it on our home page

`viewi-app\Components\Views\Home\HomePage.html`

```html
<Layout title="$title">
    <h1>$title</h1>
    <Heroes></Heroes>
</Layout>
```

Now if you refresh the page you should see the name of the hero on the page.

## Let's create a folder for models:

`viewi-app\Components\Models`

And a new model for Hero:

`viewi-app\Components\Models\HeroModel.php`

```php
<?php

namespace Components\Models;

class HeroModel
{
    public int $Id;
    public string $Name;
}
```

## Let's use it in our Heroes component:

`viewi-app\Components\Views\Heroes\Heroes.php`

```php
<?php

namespace Components\Views\Heroes;

use Components\Models\HeroModel;
use Viewi\BaseComponent;

class Heroes extends BaseComponent
{
    public HeroModel $hero;

    public function __construct()
    {
        $this->hero = new HeroModel();
        $this->hero->Id = 1;
        $this->hero->Name = 'Mastermind';
    }
}
```

`viewi-app\Components\Views\Heroes\Heroes.html`

```html
<h2>{$hero->Name} Details</h2>
<div><span>id: </span>{$hero->Id}</div>
<div><span>name: </span>{$hero->Name}</div>
```

## Format with the Uppercase:

`viewi-app\Components\Views\Heroes\Heroes.html`

```html
<h2>{strtoupper($hero->Name)} Details</h2>
<div><span>id: </span>{$hero->Id}</div>
<div><span>name: </span>{$hero->Name}</div>
```

Now when you refresh the page you will see the hero name in capital letters.

## Edit the Hero

Let's create an input that will allow us to edit the hero's name.
Using two-way binding `model="$hero->Name"` it will update the name in your component and update the view accordingly:

`viewi-app\Components\Views\Heroes\Heroes.html`

```html
<h2>{strtoupper($hero->Name)} Details</h2>
<div><span>id: </span>{$hero->Id}</div>
<div>
    <label for="name">Hero name: </label>
    <input id="name" model="$hero->Name" placeholder="name">
</div>
```

Try to refresh the page and edit the name.

# Step 5 - Display a List

## Let's create a mock for heroes

`viewi-app\Components\Mocks\HerosMocks.php`

```php
<?php

namespace Components\Mocks;

use Components\Models\HeroModel;

class HerosMocks
{
    private ?array $heroes = null;

    public function GetHeroes(): array
    {
        if ($this->heroes == null) {
            $this->heroes = [
                $this->CreateHero(1, 'Metal Man'),
                $this->CreateHero(2, 'Firefly'),
                $this->CreateHero(3, 'Mastermind'),
                $this->CreateHero(4, 'Bulletproof'),
                $this->CreateHero(5, 'Fireball'),
                $this->CreateHero(6, 'Apex'),
                $this->CreateHero(7, 'Turbine'),
                $this->CreateHero(8, 'Tarantula'),
                $this->CreateHero(9, 'Shockwave'),
                $this->CreateHero(10, 'Steamroller')
            ];
        }
        return $this->heroes;
    }

    public function CreateHero(int $id, string $name): HeroModel
    {
        $hero = new HeroModel();
        $hero->Id = $id;
        $hero->Name = $name;
        return $hero;
    }
}
```

And then use it in our component by injecting into the constructor:

`viewi-app\Components\Views\Heroes\Heroes.php`

```php
<?php

namespace Components\Views\Heroes;

use Components\Mocks\HerosMocks;
use Components\Models\HeroModel;
use Viewi\BaseComponent;

class Heroes extends BaseComponent
{
    /**
     * 
     * @var HeroModel[]
     */
    public array $heros;

    public function __construct(HerosMocks $herosMocks)
    {
        $this->heros = $herosMocks->GetHeroes();
    }
}
```

Open the template file and make some changes. 
And to iterate through the list of heros use `foreach` directive `foreach="$heros as $hero"`.

`viewi-app\Components\Views\Heroes\Heroes.html`

```html
<h2>My Heroes</h2>
<ul class="heroes">
    <li foreach="$heros as $hero">
        <span class="badge">{$hero->Id}</span> {$hero->Name}
    </li>
</ul>
```

And now when you refresh the page you will see the list of heroes.

## Let's style a little

Please create a file:

`public\css\main.css`

And copy the content from the git repository.

Now let's change our Layout. Please remove the sidebar and change the CssBundle.
It should be like this:

`viewi-app\Components\Views\Layouts\Layout.html`

```html
<!DOCTYPE html>
<html lang="en">

<head>
    <title>
        $title | Viewi
    </title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <CssBundle link="/css/main.css" />
</head>

<body>
    <div id="content">
        <slot></slot>
    </div>
    <ViewiScripts />
</body>

</html>
```

## Viewing details

Let's add a possibility to click on a hero.
Add a click event binding to the item and pass the hero as a parameter, like this:

`<li ...(click)="onSelect($hero)">`

`viewi-app\Components\Views\Heroes\Heroes.html`

```html
<h2>My Heroes</h2>
<ul class="heroes">
    <li foreach="$heros as $hero" (click)="onSelect($hero)">
        <span class="badge">{$hero->Id}</span> {$hero->Name}
    </li>
</ul>
```

## Add the event handler:

```php
public ?HeroModel $selectedHero = null;
...
public function onSelect(HeroModel $hero)
{
    $this->selectedHero = $hero;
}
```

`viewi-app\Components\Views\Heroes\Heroes.php`

```php
<?php

namespace Components\Views\Heroes;

use Components\Mocks\HerosMocks;
use Components\Models\HeroModel;
use Viewi\BaseComponent;

class Heroes extends BaseComponent
{
    /**
     * 
     * @var HeroModel[]
     */
    public array $heros;
    public ?HeroModel $selectedHero = null;

    public function __construct(HerosMocks $herosMocks)
    {
        $this->heros = $herosMocks->GetHeroes();
    }

    public function onSelect(HeroModel $hero)
    {
        $this->selectedHero = $hero;
    }
}
```

## Let's add a details for selected hero

Add these to our template:

`viewi-app\Components\Views\Heroes\Heroes.html`

```html
<h2>{strtoupper($selectedHero->Name)} Details</h2>
<div><span>id: </span>{$selectedHero->Id}</div>
<div>
    <label for="hero-name">Hero name: </label>
    <input id="hero-name" model="$selectedHero->Name" placeholder="name">
</div>
```

When you refresh the page you will get an error that says 'Trying to get property 'Name' of non-object'
and in browser's console you will se the error as well: 'Uncaught TypeError: Cannot read properties of null (reading 'Name')'

To fix that we need to display this only if there is $selectedHero available using if directive
`if="$selectedHero"`.

Let's wrap our edit into a div with the if directive. 

```html
<div if="$selectedHero">
    <h2>{strtoupper($selectedHero->Name)} Details</h2>
    <div><span>id: </span>{$selectedHero->Id}</div>
    <div>
        <label for="hero-name">Hero name: </label>
        <input id="hero-name" model="$selectedHero->Name" placeholder="name">
    </div>
</div>
```

And now try to refresh the page, click on one of the heros, and edit the name. You will notice how that name is changing accordingly on the page.

## Let's make some styling

When the user clicks on the hero, let's add a class to the `li` item.
To do that let's use the conditional attribute directive `class.selected="true"`.
In our case we want to add `selected` class if the selected hero matches the item in the list:
`class.selected="$hero === $selectedHero"`

The template should look like this:

```html
<h2>My Heroes</h2>
<ul class="heroes">
    <li foreach="$heros as $hero" (click)="onSelect($hero)" class.selected="$hero === $selectedHero">
        <span class="badge">{$hero->Id}</span> {$hero->Name}
    </li>
</ul>
<div if="$selectedHero">
    <h2>{strtoupper($selectedHero->Name)} Details</h2>
    <div><span>id: </span>{$selectedHero->Id}</div>
    <div>
        <label for="hero-name">Hero name: </label>
        <input id="hero-name" model="$selectedHero->Name" placeholder="name">
    </div>
</div>
```

# Step 6 - Create HeroDetail component

Right now we display a list and selected hero details in the same component.
Let's separate those and create HeroDetail component.

`viewi-app\Components\Views\HeroDetail\HeroDetail.php`

```php
<?php

namespace Components\Views\HeroDetail;

use Components\Models\HeroModel;
use Viewi\BaseComponent;

class HeroDetail extends BaseComponent
{
    public ?HeroModel $hero = null;    
}
```

And move hero details html part to the view, renaming $selectedHero to the $hero accordingly:

`viewi-app\Components\Views\HeroDetail\HeroDetail.html`

```html
<div if="$hero">
    <h2>{strtoupper($hero->Name)} Details</h2>
    <div><span>id: </span>{$hero->Id}</div>
    <div>
        <label for="hero-name">Hero name: </label>
        <input id="hero-name" model="$hero->Name" placeholder="name">
    </div>
</div>
```

And now in our Heroes component template remove block with $selectedHero and replace it with HeroDetail tag:

```html
<HeroDetail></HeroDetail>
````

Now we need to pass our $selectedHero into the HeroDetail component. To do this you just need to set attribute that has the name of a public property that we want to assign. In our case, based on the code `public ?HeroModel $hero = null;` it's "hero". And then set the value that we want to pass through: `hero="$selectedHero"`. Like this:

```html
<HeroDetail hero="$selectedHero"></HeroDetail>
```

The result should be:

`viewi-app\Components\Views\Heroes\Heroes.html`

```html
<h2>My Heroes</h2>
<ul class="heroes">
    <li foreach="$heros as $hero" (click)="onSelect($hero)" class.selected="$hero === $selectedHero">
        <span class="badge">{$hero->Id}</span> {$hero->Name}
    </li>
</ul>
<HeroDetail hero="$selectedHero"></HeroDetail>
```

Try to refresh the page and edit some hero. And now your code is more cleaner.

# Step 7 - Add Services

Let's continue our code separation. And this time le'ts move our data logic to the service. And leave our view components to be responsible only for the displaying the page.

Please create the HeroService:

`viewi-app\Components\Services\HeroService.php`

```php
<?php

namespace Components\Services;

use Components\Mocks\HerosMocks;

class HeroService
{
    private HerosMocks $herosMocks;
    
    public function __construct(HerosMocks $herosMocks)
    {
        $this->herosMocks = $herosMocks;
    }

    public function GetHeroes(): array
    {
        return $this->herosMocks->GetHeroes();
    }
}
```

And update the Heroes component to use the HeroService:

```php
public function __construct(HeroService $heroService)
{
    $this->heros = $heroService->GetHeroes();
}
```

`viewi-app\Components\Views\Heroes\Heroes.php`

```php
<?php

namespace Components\Views\Heroes;

use Components\Models\HeroModel;
use Components\Services\HeroService;
use Viewi\BaseComponent;

class Heroes extends BaseComponent
{
    /**
     * 
     * @var HeroModel[]
     */
    public array $heros;
    public ?HeroModel $selectedHero = null;

    public function __construct(HeroService $heroService)
    {
        $this->heros = $heroService->GetHeroes();
    }

    public function onSelect(HeroModel $hero)
    {
        $this->selectedHero = $hero;
    }
}
```

Ok, good. How about we create the Message Service and use for displaying messages on the page.

Let's create the the MessageService:

`viewi-app\Components\Services\MessageService.php`

```php
<?php

namespace Components\Services;

class MessageService
{
    /**
     * 
     * @var string[]
     */
    public array $messages = [];

    public function Add(string $message)
    {
        $this->messages[] = $message;
    }

    public function Clear()
    {
        $this->messages = [];
    }
}
```

And then, let's create a Messages component:

`viewi-app\Components\Views\Messages\Messages.php`

```php
<?php

namespace Components\Views\Messages;

use Components\Services\MessageService;
use Viewi\BaseComponent;

class Messages extends BaseComponent
{
    public MessageService $messageService;

    public function __init(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }
}
```

`viewi-app\Components\Views\Messages\Messages.html`

```html
<div if="count($messageService->messages)">
    <h2>Messages</h2>
    <button class="clear" (click)="$messageService->Clear()">Clear messages</button>
    <div foreach="$messageService->messages as $message">$message</div>
</div>
```

And now let's include it as a tag into the Home Page

`viewi-app\Components\Views\Home\HomePage.html`

```html
<Layout title="$title">
    <h1>$title</h1>
    <Heroes></Heroes>
    <Messages></Messages>
</Layout>
```

Now let's use it. Let's add some messages:

`viewi-app\Components\Services\HeroService.php`

```php
<?php

namespace Components\Services;

use Components\Mocks\HerosMocks;

class HeroService
{
    private HerosMocks $herosMocks;
    private MessageService $messageService;

    public function __construct(HerosMocks $herosMocks, MessageService $messageService)
    {
        $this->herosMocks = $herosMocks;
        $this->messageService = $messageService;
    }

    public function GetHeroes(): array
    {
        $this->messageService->Add('HeroService: fetched heroes');
        return $this->herosMocks->GetHeroes();
    }
}
```

And here:

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
    public ?HeroModel $selectedHero = null;
    private MessageService $messageService;

    public function __construct(HeroService $heroService, MessageService $messageService)
    {
        $this->heros = $heroService->GetHeroes();
        $this->messageService = $messageService;
    }

    public function onSelect(HeroModel $hero)
    {
        $this->selectedHero = $hero;
        $this->messageService->Add("Heroes component: Selected hero id={$hero->Id}");
    }
}
```

Now when you refresh the page you will see messages at the bottom. 
And every time you select a hero you will get a new message.
To clear it just click 'Clear messages' button.

# Step 8 - Add Navigation and Routes

Now it's time to break our application into the pages. And assign a route to each of them.

Let's start from removing Heroes and Messages components from our home page.

`viewi-app\Components\Views\Home\HomePage.html`

```html
<Layout title="$title">
    <h1>$title</h1>
</Layout>
```

Then we add Messages component to the Layout. And changing the title's base from Viewi to Tour of Heros:

`viewi-app\Components\Views\Layouts\Layout.html`

```html
<!DOCTYPE html>
<html lang="en">

<head>
    <title>
        $title | Tour of Heros
    </title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <CssBundle link="/css/main.css" />
</head>

<body>
    <div id="content">
        <h1>Tour of Heros</h1>
        <slot></slot>
    </div>
    <Messages></Messages>
    <ViewiScripts />
</body>

</html>
```

Now let's modify our HomePage component to display TOP 4 heroes. Let's call it a Dashboard.

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
    public array $heros;

    public function __init(HeroService $heroService)
    {
        $this->heros = array_slice($heroService->GetHeroes(), 0, 4);
    }
}
```

`viewi-app\Components\Views\Home\HomePage.html`

```html
<Layout title="Dashboard">
    <h2>Top Heroes</h2>
    <div class="heroes-menu">
        <a foreach="$heros as $hero" href="/detail/{$hero->Id}">
            {$hero->Name}
        </a>
    </div>
</Layout>
```

Now when you refresh the page you should see the Dashboard with top 4 heros. But when you click on the hero you will see page not found. Let's fix it.

First we need to set up a route for our HeroDetail component.

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

Now we need to receive that id in our HeroDetail component and get the Hero:

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

Now let's make a proper view for our page by wrapping it around Layout component and passing the title with the hero's name:

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

Now we need to get our hero. For that let's add a new method GetHero(int $id) in our HeroService and use it inside our HeroDetail component:

`viewi-app\Components\Services\HeroService.php`

```php
<?php

namespace Components\Services;

use Components\Mocks\HerosMocks;
use Components\Models\HeroModel;

class HeroService
{
    private HerosMocks $herosMocks;
    private MessageService $messageService;

    public function __construct(HerosMocks $herosMocks, MessageService $messageService)
    {
        $this->herosMocks = $herosMocks;
        $this->messageService = $messageService;
    }

    public function GetHeroes(): array
    {
        $this->messageService->Add('HeroService: fetched heroes');
        return $this->herosMocks->GetHeroes();
    }

    public function GetHero(int $id): ?HeroModel
    {
        $this->messageService->Add("HeroService: fetched hero id={$id}");
        $searchResult = array_values(array_filter(
            $this->herosMocks->GetHeroes(),
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

When you refresh the page and click on the hero you should see the details page. Also when you type the name it will update it on the page, including the page's title.

## Let's make the same for our Heroes component:

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

    public function __init(HeroService $heroService, MessageService $messageService)
    {
        $this->heros = $heroService->GetHeroes();
        $this->messageService = $messageService;
    }
}
```

`viewi-app\Components\Views\Heroes\Heroes.html`


```html
<Layout title="My Heroes">
    <h2>My Heroes</h2>
    <ul class="heroes">
        <li foreach="$heros as $hero">
            <a href="/detail/{$hero->Id}"><span class="badge">{$hero->Id}</span> {$hero->Name}</a>
        </li>
    </ul>
</Layout>
```

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

Ok cool, but how about some menu navigation. Let's create it in our Layout component:

`viewi-app\Components\Views\Layouts\Layout.html`

```html
<!DOCTYPE html>
<html lang="en">

<head>
    <title>
        $title | Tour of Heros
    </title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <CssBundle link="/css/main.css" />
</head>

<body>
    <div id="content">
        <h1>Tour of Heros</h1>
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

Now we are able to navigate through pages. The only issue left is that we can't navigate back from hero details page. Let's fix it with ClientRouter and go back button:

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

```hml
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

And that's it. Now you can navigate through pages and edit heros.

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
use Viewi\Common\JsonMapper;

class Repository
{
    private ?array $heroes = null;
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
        return $this->heroes;
    }

    public function GetById(int $id)
    {
        $this->Prepare();
        $searchResult = array_values(array_filter(
            $this->heroes,
            function (HeroModel $x) use ($id) {
                return $x->Id == $id;
            }
        ));
        return $searchResult ? $searchResult[0] : null;
    }

    private function Prepare()
    {
        if (!$this->ready) {
            $this->heroes = [];
            if (file_exists($this->fileName)) {
                $json = file_get_contents($this->fileName);
                $objects = json_decode($json, false);
                foreach ($objects as $object) {
                    $this->heroes[] = JsonMapper::Instantiate($this->className, $object);
                }
            } else {
                $this->heroes = [];
                file_put_contents($this->fileName, json_encode($this->heroes));
            }
        }
    }
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

 `$actionOrController = function..` we attached a callback function which will be executed once route will match the pattern url.

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

To make a request let's use `http->get` method. I accepts two parameters.
First one is a callback function for the successful response processing which has a response data.
And the second one is optional and will be executed if some error) has occurred.

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

# In Progress