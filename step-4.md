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
    public int $Id = 0;
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

## [Step 5 - Display a List](/step-5.md)

## [Home](/README.md#Steps)