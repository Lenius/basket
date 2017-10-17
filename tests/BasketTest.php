<?php

namespace Tests;

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

    private $basket;

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

        $this->assertEquals(150, $this->basket->total());

        $this->basket->insert([
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 150,
            'quantity' => 1,
            'weight'   => 200,
        ]);

        $this->assertEquals(300, $this->basket->total());
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

        $this->assertEquals('baz', $this->basket->item($actualId)->name);
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

        $this->assertEquals('bim', $this->basket->item($actualId)->name);
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
                [
                    'name'   => 'size',
                    'price'  => 50,
                    'weight' => 100,
                ],
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
        $weight = rand(200, 300);

        $this->basket->insert([
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 100,
            'quantity' => 1,
            'weight'   => $weight,
            'options'  => [
                [
                    'name'  => 'size',
                    'price' => 50,
                ],
            ],
        ]);

        // Test that the total weight is being calculated successfully
        $this->assertEquals($weight, $this->basket->weight());
    }

    public function testWeightOption()
    {
        $weight = rand(200, 300);
        $weight_option = rand(50, 800);
        $quantity = rand(1, 10);

        $this->basket->insert([
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 100,
            'quantity' => $quantity,
            'weight'   => $weight,
            'options'  => [
                [
                    'name'   => 'size',
                    'price'  => 50,
                    'weight' => $weight_option,
                ],
            ],
        ]);

        // Test that the total weight is being calculated successfully
        $this->assertEquals(($weight + $weight_option) * $quantity, $this->basket->weight());
    }

    public function testPriceOption()
    {
        $weight = rand(200, 300);
        $weight_option = rand(50, 800);
        $quantity = rand(1, 10);

        $this->basket->insert([
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 100,
            'tax'      => 25,
            'quantity' => 1,
            'weight'   => $weight,
            'options'  => [
                [
                    'name'   => 'size',
                    'price'  => 50,
                    'weight' => $weight_option,
                ],
                [
                    'name'   => 'color',
                    'price'  => 50,
                    'weight' => $weight_option,
                ],
            ],
        ]);

        // Test that the total price is being calculated successfully
        $this->assertEquals(250, $this->basket->total(true));
        $this->assertEquals(200, $this->basket->total(false));
    }

    public function testPriceDistractOption()
    {
        $weight = rand(200, 300);
        $weight_option = rand(50, 800);

        $this->basket->insert([
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 100,
            'tax'      => 25,
            'quantity' => 1,
            'weight'   => $weight,
            'options'  => [
                [
                    'name'   => 'size',
                    'price'  => -20,
                    'weight' => $weight_option,
                ],
                [
                    'name'   => 'color',
                    'price'  => 50,
                    'weight' => $weight_option,
                ],
            ],
        ]);

        // Test that the total price is being calculated successfully
        $this->assertEquals(162.50, $this->basket->total(true));
        $this->assertEquals(130, $this->basket->total(false));
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
        $this->assertEquals($price * $quantity, $this->basket->total());
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
        $this->assertEquals(20, $this->basket->tax());
    }

    public function testTaxOptions()
    {
        $this->basket->insert([
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 100,
            'quantity' => 1,
            'tax'      => 20,
            'weight'   => 200,
            'options'  => [
                [
                    'name'   => 'Size',
                    'value'  => 'L',
                    'weight' => 50,
                    'price'  => 100,
                   ],
                 ],
        ]);

        // Test that the tax is being calculated successfully
        $this->assertEquals(40, $this->basket->tax());
    }

    public function testTaxMultiply()
    {
        $this->basket->insert([
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 100,
            'quantity' => 2,
            'tax'      => 20,
            'weight'   => 200,
        ]);

        // Test that the tax is being calculated successfully
        $this->assertEquals(240, $this->basket->total());

        // Test that the total method can also return the pre-tax price if false is passed
        $this->assertEquals(200, $this->basket->total(false));
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
        $item->update('tax', 0);
        $this->assertEquals(100, $item->total());
        $this->assertEquals(100, $item->total(false));

        $item->update('tax', 20);
        $this->assertEquals(120, $item->total());
        $this->assertEquals(100, $item->total(false));

        $item->update('tax', 0);
        $this->assertEquals(100, $item->total());
        $this->assertEquals(100, $item->total(false));

        $item->update('tax', 20);
        $this->assertEquals(120, $item->total());
        $this->assertEquals(100, $item->total(false));
    }
}
