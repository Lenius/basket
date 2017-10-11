# Shopping Basket Package

[![Build Status](https://travis-ci.org/Lenius/basket.svg)](https://travis-ci.org/Lenius/basket) [![StyleCI](https://styleci.io/repos/12018460/shield)](https://styleci.io/repos/12018460) [![Code Coverage](https://scrutinizer-ci.com/g/Lenius/basket/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Lenius/basket/?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Lenius/basket/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Lenius/basket/?branch=master) [![Latest Stable Version](https://poser.pugx.org/Lenius/basket/v/stable)](https://packagist.org/packages/Lenius/basket) [![Total Downloads](https://poser.pugx.org/Lenius/basket/downloads)](https://packagist.org/packages/Lenius/basket) [![License](https://poser.pugx.org/Lenius/basket/license)](https://packagist.org/packages/Lenius/basket)


* [Website](http://www.lenius.dk)
* Version: production

The Lenius shopping basket composer package makes it easy to implement a shopping basket into your application and
store the basket data using one of the numerous data stores provided. You can also inject your own data store if you
would like your basket data to be stored elsewhere.

## Usage
Below is a basic usage guide for this package.

### Instantiating the basket
Before you begin, you will need to know which storage and identifier method you are going to use. The identifier is
how you store which basket is for that user. So if you store your basket in the database, then you need a cookie (or some
other way of storing an identifier) so we can link the user to a stored basket.

In this example we're going to use the cookie identifier and session for storage.

```php
use Lenius\Basket\Basket;
use Lenius\Basket\Storage\Session;
use Lenius\Basket\Identifier\Cookie;

$basket = new Basket(new Session, new Cookie);
```

### Inserting items into the basket
Inserting an item into the basket is easy. The required keys are id, name, price and quantity, although you can pass
over any custom data that you like.
```php
$basket->insert([
    'id'       => 'foo',
    'name'     => 'bar',
    'price'    => 100,
    'quantity' => 2,
    'weight' => 300
]);

```

### Inserting items with options into the basket
Inserting an item into the basket is easy. The required keys are id, name, price and quantity, although you can pass
over any custom data that you like. If option items contains price or weight there values are added to the total weight / price of the product.
```php
$basket->insert([
    'id'       => 'foo',
    'name'     => 'bar',
    'price'    => 100,
    'quantity' => 2,
    'weight' => 300,
    'options'  => [
       [
        'name' => 'Size',
        'value' => 'L'
        'weight' => 50,
        'price' => 10
       ],
     ],
]);
```

### Updating items in the basket
You can update items in your basket by updating any property on a basket item. For example, if you were within a
basket loop then you can update a specific item using the below example.
```php
foreach ($basket->contents() as $item) {
    $item->name = 'Foo';
    $item->quantity = 1;
}
```

### Removing basket items
You can remove any items in your basket by using the ```remove()``` method on any basket item.
```php
foreach ($basket->contents() as $item) {
    $item->remove();
}
```

### Destroying/emptying the basket
You can completely empty/destroy the basket by using the ```destroy()``` method.
```php
$basket->destroy()
```

### Retrieve the basket contents
You can loop the basket contents by using the following method
```php
$basket->contents();
```