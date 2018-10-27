# Shopping Basket Package

[![Build Status](https://travis-ci.org/Lenius/basket.svg)](https://travis-ci.org/Lenius/basket) [![StyleCI](https://styleci.io/repos/12018460/shield)](https://styleci.io/repos/12018460) [![Code Coverage](https://scrutinizer-ci.com/g/Lenius/basket/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Lenius/basket/?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Lenius/basket/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Lenius/basket/?branch=master) [![Latest Stable Version](https://poser.pugx.org/Lenius/basket/v/stable.svg)](https://packagist.org/packages/Lenius/basket) [![Total Downloads](https://poser.pugx.org/Lenius/basket/downloads.svg)](https://packagist.org/packages/Lenius/basket) [![License](https://poser.pugx.org/Lenius/basket/license.svg)](https://packagist.org/packages/Lenius/basket)

The Lenius shopping basket composer package makes it easy to implement a shopping basket into your application and
store the basket data using one of the numerous data stores provided. You can also inject your own data store if you
would like your basket data to be stored elsewhere.

## Installation
Using [composer](https://packagist.org/packages/lenius/basket):

```bash
$ composer require lenius/basket
```

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
Inserting an item into the basket is easy. The required keys are id, name, price, weight and quantity, although you can pass
over any custom data that you like.
```php
$basket->insert([
    'id'       => 'foo',
    'name'     => 'bar',
    'price'    => 100,
    'quantity' => 2,
    'weight'   => 300
]);

```

### Inserting items with options into the basket
Inserting an item into the basket is easy. The required keys are id, name, price and quantity, although you can pass
over any custom data that you like. If option items contains price or weight there values are added to the total weight / price of the product.
```php
$basket->insert([
    'id'         => 'foo',
    'name'       => 'bar',
    'price'      => 100,
    'quantity'   => 2,
    'weight'     => 300,
    'options'    => [
       [
        'name'   => 'Size',
        'value'  => 'L',
        'weight' => 50,
        'price'  => 10
       ],
     ],
]);
```

### Setting the tax rate for an item
Another key you can pass to your insert method is 'tax'. This is a percentage which you would like to be added onto
the price of the item.

In the below example we will use 25% for the tax rate.

```php
$basket->insert([
    'id'       => 'mouseid',
    'name'     => 'Mouse',
    'price'    => 100,
    'quantity' => 1,
    'tax'      => 25,
    'weight'   => 200
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

You can also return the Basket items as an array by passing true as the first argument
```php
$basket->contents(true);
```

### Retrieving the total items in the Basket
```php
$basket->totalItems();
```

### Retrieving the Basket total
```php
$basket->total();
```

By default the ```total()``` method will return the total value of the Basket as a ```float```, this will include
any item taxes. If you want to retrieve the Basket total without tax then you can do so by passing false to the
```total()``` method
```php
$basket->total(false);
```

### Check if the Basket has an item
```php
$basket->has($itemIdentifier);
```

### Retrieve an item object by identifier
```php
$basket->item($itemIdentifier);
```

## Basket items
There are several features of the Basket items that may also help when integrating your Basket.

### Check if an item has options
You can check if a Basket item has options by using the ```hasOptions()``` method.

```php
foreach ($basket->contents() as $item) {
    if ($item->hasOptions()) {
        // We have options
    }
}
```

### Remove an item from the Basket
```php
$item->remove();
```

### You can also get the total weight for a single item
```php
$item->weight();
```

### Output the item data as an array
```php
$item->toArray();
```
