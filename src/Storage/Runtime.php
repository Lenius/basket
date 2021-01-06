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

namespace Lenius\Basket\Storage;

use Lenius\Basket\Item;
use Lenius\Basket\ItemInterface;
use Lenius\Basket\StorageInterface;

/**
 * Class Runtime.
 */
class Runtime implements StorageInterface
{
    /** @var string */
    protected string $identifier;

    /** @var array */
    protected static array $cart = [];

    /** @var string */
    protected string $id = 'basket';

    /**
     * Add or update an item in the cart.
     *
     * @param ItemInterface $item The item to insert or update
     */
    public function insertUpdate(ItemInterface $item): void
    {
        static::$cart[$this->id][$item->identifier] = $item;
    }

    /**
     * Retrieve the cart data.
     *
     * @param bool $asArray
     *
     * @return array
     */
    public function &data($asArray = false): array
    {
        $cart = &static::$cart[$this->id];

        if (! $asArray) {
            return $cart;
        }

        $data = $cart;

        foreach ($data as &$item) {
            $item = $item->toArray();
        }

        return $data;
    }

    /**
     * Check if the item exists in the cart.
     *
     * @param string $identifier
     *
     * @return bool
     *
     * @internal param mixed $id
     */
    public function has(string $identifier): bool
    {
        foreach (static::$cart[$this->id] as $item) {
            if ($item->identifier == $identifier) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get a single cart item by id.
     *
     * @param string $identifier
     *
     * @return bool|Item
     *
     * @internal param mixed $id The item id
     */
    public function item(string $identifier)
    {
        foreach (static::$cart[$this->id] as $item) {
            if ($item->identifier == $identifier) {
                return $item;
            }
        }

        return false;
    }

    /**
     * Returns the first occurance of an item with a given id.
     *
     * @param string $id The item id
     *
     * @return bool|Item
     */
    public function find(string $id)
    {
        foreach (static::$cart[$this->id] as $item) {
            if ($item->id == $id) {
                return $item;
            }
        }

        return false;
    }

    /**
     * Remove an item from the cart.
     *
     * @param string $id
     */
    public function remove(string $id): void
    {
        unset(static::$cart[$this->id][$id]);
    }

    /**
     * Destroy the cart.
     */
    public function destroy(): void
    {
        static::$cart[$this->id] = [];
    }

    /**
     * Set the cart identifier.
     *
     * @param string $identifier
     *
     * @internal param string $identifier
     */
    public function setIdentifier(string $identifier): void
    {
        $this->id = $identifier;

        if (! array_key_exists($this->id, static::$cart)) {
            static::$cart[$this->id] = [];
        }
    }

    /**
     * Return the current cart identifier.
     *
     * @return string The identifier
     */
    public function getIdentifier(): string
    {
        return $this->id;
    }

    /**
     * Restore the cart.
     */
    public function restore(): void
    {
        if (isset($_SESSION['cart'])) {
            static::$cart = unserialize($_SESSION['cart']);
        }
    }
}
