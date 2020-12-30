<?php

/**
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
 * @see https://github.com/lenius/basket
 */

namespace Lenius\Basket;

/**
 * Class Item.
 *
 * @property string $identifier
 * @property float  $price
 * @property int    $quantity
 * @property float  $weight
 */
class Item implements ItemInterface
{
    /** @var string */
    protected $identifier;

    /** @var Tax */
    protected $tax;

    /** @var array */
    protected $data = [];

    /**
     * Construct the item.
     */
    public function __construct(array $item)
    {
        foreach ($item as $key => $value) {
            $this->data[$key] = $value;
        }

        $item['tax'] = isset($item['tax']) ? $item['tax'] : 0;

        $this->tax = new Tax($item['tax']);
    }

    /**
     * Set identifier.
     *
     * @param mixed $identifier
     *
     * @return mixed|void
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * Return the value of protected methods.
     *
     * @param mixed $param
     *
     * @return mixed
     */
    public function __get($param)
    {
        return 'identifier' == $param ? $this->identifier : $this->data[$param];
    }

    /**
     * Update data array using set magic method.
     *
     * @param string $param The key to set
     * @param mixed  $value The value to set $param to
     */
    public function __set($param, $value)
    {
        $this->data[$param] = $value;

        if ('tax' == $param) {
            $this->tax = new Tax($value);
        }
    }

    /**
     * Return the total tax for this item.
     *
     * @return float
     */
    public function tax()
    {
        $price = $this->totalPrice();
        $quantity = $this->quantity;

        return $this->tax->rate($price * $quantity);
    }

    /**
     * Return the total price for this item.
     *
     * @return float
     */
    private function totalPrice()
    {
        $price = $this->price;

        if ($this->hasOptions()) {
            foreach ($this->data['options'] as $item) {
                if (array_key_exists('price', $item)) {
                    $price += $item['price'];
                }
            }
        }

        return (float) $price;
    }

    /**
     * Return the total of the item, with or without tax.
     *
     * @param bool $includeTax Whether or not to include tax
     *
     * @return float The total, as a float
     */
    public function total($includeTax = true)
    {
        $price = $this->totalPrice();

        if ($includeTax) {
            $price = $this->tax->add($price);
        }

        return (float) ($price * $this->quantity);
    }

    /**
     * Return the total weight of the item.
     *
     * @return float The weight, as a float
     */
    public function weight()
    {
        $weight = $this->weight;

        if ($this->hasOptions()) {
            foreach ($this->data['options'] as $item) {
                if (array_key_exists('weight', $item)) {
                    $weight += $item['weight'];
                }
            }
        }

        return (float) ($weight * $this->quantity);
    }

    /**
     * Return the total of the item, with or without tax.
     *
     * @param bool $includeTax Whether or not to include tax
     *
     * @return float The total, as a float
     */
    public function single($includeTax = true)
    {
        $price = $this->totalPrice();

        if ($includeTax) {
            $price = $this->tax->add($price);
        }

        return (float) $price;
    }

    /**
     * Update a single key for this item, or multiple.
     *
     * @param mixed $key   The array key to update, or an array of key-value pairs to update
     * @param mixed $value
     */
    public function update($key, $value = null)
    {
        if ($key instanceof ItemInterface) {
            foreach ($key->toArray() as $updateKey => $updateValue) {
                $this->update($updateKey, $updateValue);
            }
        } else {
            if ('tax' == $key && is_numeric($value)) {
                $this->tax = new Tax(floatval($value));
            } else {
                // update the item
                $this->data[$key] = $value;
            }
        }
    }

    /**
     * Check if this item has options.
     *
     * @return bool Yes or no?
     */
    public function hasOptions()
    {
        return array_key_exists('options', $this->data) && ! empty($this->data['options']);
    }

    /**
     * Convert the item into an array.
     *
     * @return array The item data
     */
    public function toArray()
    {
        return $this->data;
    }
}
