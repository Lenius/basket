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
 * @version production
 *
 * @link http://github.com/lenius/basket
 */

namespace Lenius\Basket;

/**
 * @property mixed name
 * @property mixed price
 * @property mixed quantity
 * @property mixed weight
 * @property mixed options
 */
class Item
{
    protected $identifier;
    protected $store;
    protected $tax;

    protected $data = [];

    /**
     * Construct the item.
     *
     * @param string           $identifier
     * @param array            $item
     * @param StorageInterface $store
     */
    public function __construct($identifier, array $item, StorageInterface $store)
    {
        $this->identifier = $identifier;
        $this->store = $store;

        foreach ($item as $key => $value) {
            $this->data[$key] = $value;
        }

        $item['tax'] = isset($item['tax']) ? $item['tax'] : 0;

        $this->tax = new Tax($item['tax']);
    }

    /**
     * Return the value of protected methods.
     *
     * @param any $param
     *
     * @return mixed
     */
    public function __get($param)
    {
        return $param == 'identifier' ? $this->identifier : $this->data[$param];
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
        if ($param == 'tax') {
            $this->tax = new Tax($value);
        }
    }

    /**
     * Removes the current item from the cart.
     *
     * @return void
     */
    public function remove()
    {
        $this->store->remove($this->identifier);
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

        return $price;
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
     * @param array|string $key   The array key to update, or an array of key-value pairs to update
     * @param null         $value
     *
     * @return void
     */
    public function update($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $updateKey => $updateValue) {
                $this->update($updateKey, $updateValue);
            }
        } else {
            // Update the item
            $this->data[$key] = $value;
            if ($key == 'tax') {
                $this->tax = new Tax($value);
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
        return array_key_exists('options', $this->data) && !empty($this->data['options']);
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
