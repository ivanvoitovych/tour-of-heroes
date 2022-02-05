# Step 4 - The Hero Editor

## Let's create the heroes component:

Please create a component and name it `Heroes`. Add a public property `$hero` with a default value - 'Mastermind'. Please output a $hero property inside the `h2` tag in the template file.

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

Once you have created a component, you can use it as a tag in other templates. For example, let's add the `Heroes` component to our home page. Once you include it as a tag, it will instantiate a new instance of that component and render its template content instead of a tag.

`<Heroes></Heroes>` will be replaced with `<h2>Mastermind</h2>`.

`viewi-app\Components\Views\Home\HomePage.html`

```html
<Layout title="$title">
    <h1>$title</h1>
    <Heroes></Heroes>
</Layout>
```

If you refresh the page, you should see the Hero's name on the page.

## Let's create a folder for models:

In real life, the data will probably be a more complex structure. Let's change our $hero property type from `string` to some model class.

Create a folder:

`viewi-app\Components\Models`

And a new model for Hero with `$id` and `$Name` properties:

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

Please change the type of $hero property to `HeroModel` and instantiate a dummy value in the constructor.

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

Now our `$hero` is an object, and to interpolate properties of the object, we need to use single mustache syntax:

`<h2>{$hero->Name}</h2>`

Let's render Hero's properties in the template:

`viewi-app\Components\Views\Heroes\Heroes.html`

```html
<h2>{$hero->Name} Details</h2>
<div><span>id: </span>{$hero->Id}</div>
<div><span>name: </span>{$hero->Name}</div>
```

## Format with the Uppercase:

The good thing is you can use built-in functions and component methods to format values. For example, let's use `strtoupper` to output Hero's name in uppercase:

`viewi-app\Components\Views\Heroes\Heroes.html`

```html
<h2>{strtoupper($hero->Name)} Details</h2>
<div><span>id: </span>{$hero->Id}</div>
<div><span>name: </span>{$hero->Name}</div>
```

When you refresh the page, you will see the Hero's name in capital letters.

## Edit the Hero

Let's create an input that will allow us to edit the Hero's name.
Using two-way binding `<input model="$hero->Name">` it will update the name in your component and update the view accordingly.
More about input bindings here: [https://viewi.net/docs/input-bindings](https://viewi.net/docs/input-bindings).

`viewi-app\Components\Views\Heroes\Heroes.html`

```html
<h2>{strtoupper($hero->Name)} Details</h2>
<div><span>id: </span>{$hero->Id}</div>
<div>
    <label for="name">Hero name: </label>
    <input id="name" model="$hero->Name" placeholder="name">
</div>
```

Try to refresh the page and edit the name. You will see changes instantly without interacting with the server. Like it would be with your favorite javascript framework.

## [Step 5 - Display a List](/step-5.md)

## [Home](/README.md#Steps)