# Step 6 - Create HeroDetail component

We display a list and selected hero details in the same component.
Let's separate those and create the `HeroDetail` component. It will have the `$hero` property with a null value by default.

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

Please move the hero details HTML part to the view, renaming `$selectedHero` to the `$hero` accordingly:

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

And now, in our `Heroes` component template, please remove block with $selectedHero and replace it with HeroDetail tag, like this:

```html
<HeroDetail></HeroDetail>
````

We need to pass our `$selectedHero` into the `HeroDetail` component. To do this, you need to set an attribute with the name of a public property that we want to assign. 

In our case, based on the code 
`public ?HeroModel $hero = null;` it's "hero". 

And then set the value that we want to pass through: `<HeroDetail hero="$selectedHero"`, like this:

```html
<HeroDetail hero="$selectedHero"></HeroDetail>
```

More about passing data through properties here: [https://viewi.net/docs/components-basics#passing-data](https://viewi.net/docs/components-basics#passing-data)

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

Now your code is cleaner. Try to refresh the page and edit some Hero.

## [Step 7 - Add Services](/step-7.md)

## [Home](/README.md#Steps)