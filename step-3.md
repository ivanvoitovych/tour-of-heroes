# Step 3

When you open a browser, you will see some content already.

The code for this page is located here:

`viewi-app\Components\Views\Home\HomePage.php`

and here:

`viewi-app\Components\Views\Home\HomePage.html`

As you can see, the component itself is a PHP class that extends the `BaseComponent` abstract class. The component is tightly coupled with the template file, which has the same base name. To create a new Viewi component, you will need to create two files with the same base name: one for PHP and another for HTML template. 

To output something in the template file, you can use interpolation.:

`<div>$title</div>` - will render $title property from your component iside of a div.

More about a template syntax here: [https://viewi.net/docs/syntax](https://viewi.net/docs/syntax).

Let's change the title on our home page:

`viewi-app\Components\Views\Home\HomePage.php`

`public string $title = 'Tour of Heroes';`

When you refresh the page, you will see a new title.

## [Step 4 - The Hero Editor](/step-4.md)

## [Home](/README.md#Steps)