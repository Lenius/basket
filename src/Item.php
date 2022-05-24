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
    protected string $identifier;

    /** @var Tax */
    protected Tax $tax;

    /** @var array */
    protected array $data = [];

    /**
     * Construct the item.
     * @param array $item
     */
    public function __construct(array $item)
    {
        foreach ($item as $key => $value) {
            $this->data[$key] = $value;
        }

        $item['tax'] = $item['tax'] ?? 0;

        $this->tax = new Tax($item['tax']);
    }

    /**
     * Set identifier.
     *
     * @param string $identifier
     */
    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    /**
     * Return the value of protected methods.
     *
     * @param string $param
     *
     * @return mixed
     */
    public function __get(string $param)
    {
        return 'identifier' === $param ? $this->identifier : $this->data[$param];
    }

    /**
     * Update data array using set magic method.
     *
     * @param mixed $param The key to set
     * @param mixed $value The value to set $param to
     */
    public function __set(mixed $param, mixed $value): void
    {
        $this->data[$param] = $value;

        if ('tax' == $param) {
            $this->tax = new Tax(floatval($value));
        }
    }

    /**
     * Return the total tax for this item.
     *
     * @return float
     */
    public function tax(): float
    {
        return $this->tax->rate($this->totalPrice() * $this->getQuantity());
    }

    /**
     * Return the total price for this item.
     *
     * @return float
     */
    private function totalPrice(): float
    {
        $price = $this->data['price'];

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
    public function total(bool $includeTax = true): float
    {
        $price = $this->totalPrice();

        if ($includeTax) {
            $price = $this->tax->add($price);
        }

        return ($price * $this->getQuantity());
    }

    /**
     * Return the total weight of the item.
     *
     * @return float The weight, as a float
     */
    public function weight(): float
    {
        $weight = $this->data['weight'];

        if ($this->hasOptions()) {
            foreach ($this->data['options'] as $item) {
                if (array_key_exists('weight', $item)) {
                    $weight += $item['weight'];
                }
            }
        }

        return (float) ($weight * $this->getQuantity());
    }

    /**
     * Return the total of the item, with or without tax.
     *
     * @param bool $includeTax Whether or not to include tax
     *
     * @return float The total, as a float
     */
    public function single(bool $includeTax = true): float
    {
        $price = $this->totalPrice();

        if ($includeTax) {
            $price = $this->tax->add($price);
        }

        return $price;
    }

    /**
     * Update a single key for this item, or multiple.
     *
     * @param mixed $key   The array key to update, or an array of key-value pairs to update
     * @param mixed|null $value
     */
    public function update(mixed $key, mixed $value = null): void
    {
        if ($key instanceof ItemInterface) {
            foreach ($key->toArray() as $updateKey => $updateValue) {
                $this->update($updateKey, $updateValue);
            }
        } else {
            if ('tax' == $key && is_numeric($value)) {
                $this->tax = new Tax((float)$value);
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
    public function hasOptions(): bool
    {
        return array_key_exists('options', $this->data) && ! empty($this->data['options']);
    }

    /**
     * Convert the item into an array.
     *
     * @return array The item data
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * Return quantity
     *
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->data['quantity'];
    }

    /**
     * Set quantity
     *
     * @param int $quantity
     *
     * @return void
     */
    public function setQuantity(int $quantity): void
    {
        $this->data['quantity'] = $quantity;
    }
}
