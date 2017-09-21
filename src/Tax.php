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

namespace Lenius\Basket;

class Tax
{
    protected $percentage;
    protected $deductModifier;
    protected $addModifier;

    /**
     * When constructing the tax class, you can either
     * pass in a percentage, or a price before and after
     * tax and have the library work out the tax rate
     * automatically.
     *
     * @param float $value The percentage of your tax (or price before tax)
     * @param float $after The value after tax
     */
    public function __construct($value, $after = null)
    {
        $this->percentage = $value;

        if (is_numeric($after)) {
            $this->percentage = (($after - $value) / $value) * 100;
        }

        $this->deductModifier = 1 - ($this->percentage / 100);
        $this->addModifier = 1 + ($this->percentage / 100);
    }

    /**
     * Deduct tax from a specified price.
     *
     * @param float $price The price you want to deduct tax from
     *
     * @return float $price - tax
     */
    public function deduct($price)
    {
        return $price * $this->deductModifier;
    }

    /**
     * Add tax to a specified price.
     *
     * @param float $price The value you want to add tax to
     *
     * @return float $price + tax
     */
    public function add($price)
    {
        return $price * $this->addModifier;
    }

    /**
     * Calculate the tax rate from a price.
     *
     * @param float $price The price (after tax)
     *
     * @return float The tax rate
     */
    public function rate($price)
    {
        return $price - $this->deduct($price);
    }

    /**
     * Return the value of protected properties.
     *
     * @param mixed $property The property
     *
     * @return mixed The value of the property
     */
    public function __get($property)
    {
        return $this->$property;
    }
}
