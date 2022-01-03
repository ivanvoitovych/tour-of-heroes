# Tour of Heroes app and tutorial

-----------
Borrowed from Angular [Tour of Heroes](https://angular.io/tutorial)

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

`viewi-app\Components\Models\HeroMode.php`

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
    public function GetHeroes(): array
    {
        $heroes = [
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
        return $heroes;
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





# In Progress