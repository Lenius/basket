<?php

namespace Lenius\Basket\Tests;

/*
 * This file is part of Lenius Basket, a PHP package to handle
 * your shopping basket.
 *
 * Copyright (c) 2017 Lenius.
 * https://github.com/lenius/basket
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Carsten Jonstrup<info@lenius.dk>
 * @copyright 2017 Lenius.
 *
 * @version production
 *
 * @link https://github.com/lenius/basket
 */
use Lenius\Basket\Tax;
use PHPUnit\Framework\TestCase;

class TaxTest extends TestCase
{
    private $item;

    public function setUp(): void
    {
    }

    public function tearDown(): void
    {
        $this->item = null;
    }

    public function testTaxValue(): void
    {
        $value = 25;

        $this->item = new Tax($value);

        $this->assertEquals(125, $this->item->add(100));
        $this->assertEquals(25, $this->item->rate(100));
        $this->assertEquals(25, $this->item->percentage);
        $this->assertEquals(0.75, $this->item->deductModifier);
        $this->assertEquals(1.25, $this->item->addModifier);
        $this->assertEquals(75, $this->item->deduct(100));
    }

    public function testTaxValueAfter(): void
    {
        $value = 100;
        $afterValue = 50;

        $this->item = new Tax($value, $afterValue);

        $this->assertEquals(-50, $this->item->percentage);
        $this->assertEquals(1.5, $this->item->deductModifier);
        $this->assertEquals(0.5, $this->item->addModifier);
    }
}
