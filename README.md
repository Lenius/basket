# Shopping Basket Package

[![Build Status](https://travis-ci.org/Lenius/basket.png?branch=master)](http://travis-ci.org/Lenius/basket)
[![Total Downloads](https://poser.pugx.org/lenius/basket/downloads.svg)](https://packagist.org/packages/lenius/basket)

* [Website](http://www.lenius.dk)
* [License](https://github.com/lenius/basket/master/LICENSE)
* Version: dev

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
$basket->insert(array(
    'id'       => 'foo',
    'name'     => 'bar',
    'price'    => 100,
    'quantity' => 2,
    'weight' => 300
));
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