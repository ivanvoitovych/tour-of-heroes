# Step 5 - Display a List

## Let's create a mock for heroes

To simulate real-world data, let's create a list of Heroes. Please make the `HeroesMocks` file and copy the following content. Later, we will create a backend API and use the server's data.

`viewi-app\Components\Mocks\HeroesMocks.php`

```php
<?php

namespace Components\Mocks;

use Components\Models\HeroModel;

class HeroesMocks
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

And then use it in our component by injecting it into the constructor:

`viewi-app\Components\Views\Heroes\Heroes.php`

```php
<?php

namespace Components\Views\Heroes;

use Components\Mocks\HeroesMocks;
use Components\Models\HeroModel;
use Viewi\BaseComponent;

class Heroes extends BaseComponent
{
    /**
     * 
     * @var HeroModel[]
     */
    public array $heroes;

    public function __construct(HeroesMocks $heroesMocks)
    {
        $this->heroes = $heroesMocks->GetHeroes();
    }
}
```

Viewi handles dependency injections automatically for you. No need to set up anything. More about it here: [https://viewi.net/docs/di](https://viewi.net/docs/di).

Please, open the template file, and let's output the list of heroes. 
To iterate through the list of heroes, please use a `foreach` directive as an attribute: `<li foreach="$heroes as $hero">`.

`viewi-app\Components\Views\Heroes\Heroes.html`

```html
<h2>My Heroes</h2>
<ul class="heroes">
    <li foreach="$heroes as $hero">
        <span class="badge">{$hero->Id}</span> {$hero->Name}
    </li>
</ul>
```

And now, when you refresh the page, you will see the list of heroes.

## Let's style a little

Please create a file:

`public\css\main.css`

And copy the content from the git repository.

[/public/css/main.css](https://raw.githubusercontent.com/ivanvoitovych/tour-of-heroes/master/public/css/main.css)

Now let's change our Layout. Please remove the sidebar and change the CssBundle. It should be like this:

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

Notice `<slot></slot>` here. It allows you to output inner content to your child component. More about it here: [https://viewi.net/docs/components-basics#slots](https://viewi.net/docs/components-basics#slots)

For example, everything inside the Layout tag in `HomePage` will be rendered in place of `<slot></slot>` in the `Layout` component.

```html
<Layout title="$title">
    <h1>$title</h1>
</Layout>
```

More about `CssBundle` here: [https://viewi.net/docs/css-bundle](https://viewi.net/docs/css-bundle)

## Viewing details

Let's add a possibility to click on a hero.
Add a click event binding to the item and pass the hero as a parameter, like this:

`<li ...(click)="onSelect($hero)">`

What happens here is when the user clicks on the `li` item, the `onSelect` method of your component will be called, and as a parameter, it will pass `$hero` from the current `foreach` iteration.

More about event handling here: [https://viewi.net/docs/events](https://viewi.net/docs/events)

`viewi-app\Components\Views\Heroes\Heroes.html`

```html
<h2>My Heroes</h2>
<ul class="heroes">
    <li foreach="$heroes as $hero" (click)="onSelect($hero)">
        <span class="badge">{$hero->Id}</span> {$hero->Name}
    </li>
</ul>
```

## Add the event handler:

To handle the event, we need a method. Let's create one. It will receive a `HeroModel` model and assign it to the `$selectedHero` property.

```php
public ?HeroModel $selectedHero = null;
...
public function onSelect(HeroModel $hero)
{
    $this->selectedHero = $hero;
}
```

Now, your component should look something like this:

`viewi-app\Components\Views\Heroes\Heroes.php`

```php
<?php

namespace Components\Views\Heroes;

use Components\Mocks\HeroesMocks;
use Components\Models\HeroModel;
use Viewi\BaseComponent;

class Heroes extends BaseComponent
{
    /**
     * 
     * @var HeroModel[]
     */
    public array $heroes;
    public ?HeroModel $selectedHero = null;

    public function __construct(HeroesMocks $heroesMocks)
    {
        $this->heroes = $heroesMocks->GetHeroes();
    }

    public function onSelect(HeroModel $hero)
    {
        $this->selectedHero = $hero;
    }
}
```

## Let's add some details for the selected hero

Please update the template to show `$selectedHero` details.

`viewi-app\Components\Views\Heroes\Heroes.html`

```html
<h2>{strtoupper($selectedHero->Name)} Details</h2>
<div><span>id: </span>{$selectedHero->Id}</div>
<div>
    <label for="hero-name">Hero name: </label>
    <input id="hero-name" model="$selectedHero->Name" placeholder="name">
</div>
```

When you refresh the page, you will get an error that says 'Trying to get property 'Name' of non-object.'
And in the browser's console, you will see the error as well: 'Uncaught TypeError: Cannot read properties of null (reading 'Name').

It happens because `$selectedHero` is null by default, and you are trying to access its property.

To fix that, we need to display this only if there is `$selectedHero` available using the `if` directive:
`<div if="$selectedHero"`.

More about it here: [https://viewi.net/docs/conditional-rendering](https://viewi.net/docs/conditional-rendering)

Let's wrap our edit into a div with the `if` directive. 

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

And now try to refresh the page, click on one of the heroes, and edit the name. You will notice how that name is changing accordingly on the page.

## Let's make some styling

When the user clicks on the hero, let's add a class to the `li` item.
To do that let's use the conditional attribute directive `class.selected="true"`.
In our case we want to add `selected` class if the selected hero matches the item in the list:
`class.selected="$hero === $selectedHero"`

More about conditional attributes here: [https://viewi.net/docs/syntax#conditional-attributes](https://viewi.net/docs/syntax#conditional-attributes)

The template should look like this:

```html
<h2>My Heroes</h2>
<ul class="heroes">
    <li foreach="$heroes as $hero" (click)="onSelect($hero)" class.selected="$hero === $selectedHero">
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

## [Step 6 - Create HeroDetail component](/step-6.md)

## [Home](/README.md#Steps)