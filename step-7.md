# Step 7 - Add Services

Let's continue our code separation. And this time le'ts move our data logic to the service. And leave our view components to be responsible only for the displaying of the page.

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

## [Step 8 - Add Navigation and Routes](/step-8.md)

## [Home](/README.md#Steps)