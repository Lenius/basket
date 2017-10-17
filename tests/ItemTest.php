<?php

namespace Tests;

/*
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
 * @version production
 *
 * @link http://github.com/lenius/basket
 */
use Lenius\Basket\Basket;
use Lenius\Basket\Identifier\Runtime as RuntimeIdentifier;
use Lenius\Basket\Item;
use Lenius\Basket\Storage\Runtime as RuntimeStore;
use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase
{
    private $item;

    public function setUp()
    {

    }

    public function tearDown()
    {
        $this->item = null;
    }

    public function testTaxUpdate()
    {
        $identifier = 1;

        $item = [
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 100,
            'quantity' => 1,
            'weight'   => 200,
        ];


        $this->item = new Item($identifier, $item, new RuntimeStore());

        $this->item->update('tax', 0);
        $this->assertEquals(100, $this->item->total());
        $this->assertEquals(100, $this->item->total(false));

        $this->item->update('tax', 20);
        $this->assertEquals(120, $this->item->total());
        $this->assertEquals(100, $this->item->total(false));
    }

    public function testWeight()
    {
        $identifier = 1;

        $weight = rand(200, 300);
        $quantity = rand(1, 10);

        $item = [
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 100,
            'quantity' => $quantity,
            'weight'   => $weight,
        ];

        $this->item = new Item($identifier, $item, new RuntimeStore());
        $this->assertEquals(($weight * $quantity), $this->item->weight());
    }

    public function testWeightWithOption()
    {
        $identifier = 1;

        $weight = rand(200, 300);
        $weight_option = rand(50, 800);
        $quantity = rand(1, 10);

        $item = [
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 100,
            'quantity' => $quantity,
            'weight'   => $weight,
            'options'  => [
                [
                    'name'   => 'Size',
                    'value'  => 'L',
                    'weight' => $weight_option,
                    'price'  => 100,
                ],
            ],
        ];

        $this->item = new Item($identifier, $item, new RuntimeStore());
        $this->assertEquals(($weight + $weight_option) * $quantity, $this->item->weight());
    }

    public function testHasOption()
    {
        $identifier = 1;

        $item = [
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 100,
            'quantity' => 1,
            'weight'   => 200,
            'options'  => [
                [
                    'name'   => 'Size',
                    'value'  => 'L',
                    'weight' => 50,
                    'price'  => 100,
                ],
            ],
        ];

        $this->item = new Item($identifier, $item, new RuntimeStore());
        $this->assertTrue($this->item->hasOptions());

    }

    public function testHasNotOption()
    {
        $identifier = 1;

        $item = [
            'id'       => 'foo',
            'name'     => 'bar',
            'price'    => 100,
            'quantity' => 1,
            'weight'   => 200,
            'options'  => [
                [
                    'name'   => 'Size',
                    'value'  => 'L',
                    'weight' => 50,
                    'price'  => 100,
                ],
            ],
        ];

        $this->item = new Item($identifier, $item, new RuntimeStore());
        $this->assertEquals(250, $this->item->weight());
    }

}
