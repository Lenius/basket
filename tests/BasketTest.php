<?php

/**
 * This file is part of Lenius Basket, a PHP package to handle
 * your shopping basket.
 *
 * Copyright (c) 2017 Lenius.
 * http://github.com/lenius/basket
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Carsten Jonstrup<info@lenius.dk>
 * @copyright 2017 Lenius.
 *
 * @version dev
 *
 * @link http://github.com/lenius/basket
 */
use Lenius\Basket\Basket;
use Lenius\Basket\Identifier\Runtime as RuntimeIdentifier;
use Lenius\Basket\Storage\Runtime as RuntimeStore;
use PHPUnit\Framework\TestCase;

class BasketTest extends TestCase
{
    public function setUp()
    {
        $this->basket = new Basket(new RuntimeStore(), new RuntimeIdentifier());
    }

    public function tearDown()
    {
        $this->basket->destroy();
    }

    public function testInsert()
    {
        $actualId = $this->basket->insert([
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 100,
            'quantity' => 1,
            'weight'   => 200,
        ]);

        $identifier = md5('foo'.serialize([]));

        $this->assertEquals($identifier, $actualId);
    }

    public function testInsertIncrements()
    {
        $this->basket->insert([
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 150,
            'quantity' => 1,
            'weight'   => 200,
        ]);

        $this->assertEquals($this->basket->total(), 150);

        $this->basket->insert([
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 150,
            'quantity' => 1,
            'weight'   => 200,
        ]);

        $this->assertEquals($this->basket->total(), 300);
    }

    public function testUpdate()
    {
        $actualId = $this->basket->insert([
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 100,
            'quantity' => 1,
            'weight'   => 200,
        ]);

        $this->basket->update($actualId, 'name', 'baz');

        $this->assertEquals($this->basket->item($actualId)->name, 'baz');
    }

    public function testMagicUpdate()
    {
        $actualId = $this->basket->insert([
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 100,
            'quantity' => 1,
            'weight'   => 200,
        ]);

        foreach ($this->basket->contents() as $item) {
            $item->name = 'bim';
        }

        $this->assertEquals($this->basket->item($actualId)->name, 'bim');
    }

    public function testOptions()
    {
        $actualId = $this->basket->insert([
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 100,
            'quantity' => 1,
            'weight'   => 200,
            'options'  => [
                'size' => 'L',
            ],
        ]);

        $item = $this->basket->item($actualId);

        $this->assertTrue($item->hasOptions());
        $this->assertNotEmpty($item->options);

        $item->options = [];

        $this->assertFalse($item->hasOptions());
        $this->assertEmpty($item->options);
    }

    public function testWeight()
    {
        $weight = 200;

        $this->basket->insert([
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 100,
            'quantity' => 1,
            'weight'   => $weight,
            'options'  => [
                'size' => 'L',
            ],
        ]);

        // Test that the total weight is being calculated successfully
        $this->assertEquals($this->basket->weight(), $weight);
    }

    public function testWeightOption()
    {
        $weight = 200;
        $weight_option = 50;

        $this->basket->insert([
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 100,
            'quantity' => 1,
            'weight'   => $weight,
            'options'  => [
                'size'   => 'L',
                'weight' => $weight_option,
            ],
        ]);

        // Test that the total weight is being calculated successfully
        $this->assertEquals($this->basket->weight(), $weight + $weight_option);
    }

    public function testFind()
    {
        $this->basket->insert([
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 100,
            'quantity' => 1,
            'weight'   => 200,
        ]);

        $this->assertInstanceOf('\Lenius\Basket\Item', $this->basket->find('foo'));
    }

    public function testTotals()
    {
        // Generate a random price and quantity
        $price = rand(20, 99999);
        $quantity = rand(1, 10);

        $this->basket->insert([
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => $price,
            'quantity' => $quantity,
            'weight'   => 200,
        ]);

        // Test that the total is being calculated successfully
        $this->assertEquals($this->basket->total(), $price * $quantity);
    }

    public function testTotalItems()
    {
        $adding = rand(1, 200);
        $actualTotal = 0;

        for ($i = 1; $i <= $adding; $i++) {
            $quantity = rand(1, 20);

            $this->basket->insert([
                'id'       => uniqid(),
                'name'     => 'bar',
                'price'    => 100,
                'quantity' => $quantity,
                'weight'   => 200,
            ]);

            $actualTotal += $quantity;
        }

        $this->assertEquals($this->basket->totalItems(), $actualTotal);
        $this->assertEquals($this->basket->totalItems(true), $adding);
    }

    public function testItemRemoval()
    {
        $this->basket->insert([
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 100,
            'quantity' => 1,
            'weight'   => 200,
        ]);

        $contents = &$this->basket->contents();

        $this->assertNotEmpty($contents);

        foreach ($contents as $item) {
            $item->remove();
        }

        $this->assertEmpty($contents);
    }

    public function testAlternateItemRemoval()
    {
        $this->basket->insert([
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 100,
            'quantity' => 1,
            'weight'   => 200,
        ]);

        $contents = &$this->basket->contents();

        $this->assertNotEmpty($contents);

        foreach ($contents as $identifier => $item) {
            $this->basket->remove($identifier);
        }

        $this->assertEmpty($contents);
    }

    public function testItemToArray()
    {
        $actualId = $this->basket->insert([
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 100,
            'quantity' => 1,
            'weight'   => 200,
        ]);

        $this->assertTrue(is_array($this->basket->item($actualId)->toArray()));
    }

    public function testbasketToArray()
    {
        $this->basket->insert([
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 100,
            'quantity' => 1,
            'weight'   => 200,
        ]);

        foreach ($this->basket->contents(true) as $item) {
            $this->assertTrue(is_array($item));
        }
    }

    public function testTax()
    {
        $this->basket->insert([
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 100,
            'quantity' => 1,
            'tax'      => 20,
            'weight'   => 200,
        ]);

        // Test that the tax is being calculated successfully
        $this->assertEquals($this->basket->total(), 120);

        // Test that the total method can also return the pre-tax price if false is passed
        $this->assertEquals($this->basket->total(false), 100);
    }

    public function testTaxUpdate()
    {
        $this->basket->insert([
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 100,
            'quantity' => 1,
            'tax'      => 20,
            'weight'   => 200,
        ]);

        $identifier = md5('foo'.serialize([]));

        $item = $this->basket->item($identifier);

        // Test that the tax is being calculated successfully
        $item->tax = 0;
        $this->assertEquals($item->total(), 100);
        $this->assertEquals($item->total(false), 100);

        $item->tax = 20;
        $this->assertEquals($item->total(), 120);
        $this->assertEquals($item->total(false), 100);

        $item->update('tax', 0);
        $this->assertEquals($item->total(), 100);
        $this->assertEquals($item->total(false), 100);

        $item->update('tax', 20);
        $this->assertEquals($item->total(), 120);
        $this->assertEquals($item->total(false), 100);
    }
}
