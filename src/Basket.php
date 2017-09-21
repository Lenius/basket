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

use InvalidArgumentException;

class Basket
{
    protected $id;

    protected $identifier;
    protected $store;

    protected $currency;

    protected $requiredParams = [
        'id',
        'name',
        'quantity',
        'price',
        'weight',
    ];

    /**
     * Basket constructor.
     *
     * @param StorageInterface    $store      The interface for storing the cart data
     * @param IdentifierInterface $identifier The interface for storing the identifier
     */
    public function __construct(StorageInterface $store, IdentifierInterface $identifier)
    {
        $this->store = $store;
        $this->identifier = $identifier;

        // Generate/retrieve identifier
        $this->id = $this->identifier->get();

        // Restore the cart from a saved version
        if (method_exists($this->store, 'restore')) {
            $this->store->restore();
        }

        // Let our storage class know which cart we're talking about
        $this->store->setIdentifier($this->id);
    }

    /**
     * Retrieve the basket contents.
     *
     * @param bool $asArray
     *
     * @return array An array of Item objects
     */
    public function &contents($asArray = false)
    {
        return $this->store->data($asArray);
    }

    /**
     * Insert an item into the basket.
     *
     * @param array $item An array of item data
     *
     * @return string A unique item identifier
     */
    public function insert(array $item)
    {
        $this->checkArgs($item);

        $itemIdentifier = $this->createItemIdentifier($item);

        if ($this->has($itemIdentifier)) {
            $item['quantity'] = $this->item($itemIdentifier)->quantity + $item['quantity'];
            $this->update($itemIdentifier, $item);

            return $itemIdentifier;
        }

        $item = new Item($itemIdentifier, $item, $this->store);

        $this->store->insertUpdate($item);

        return $itemIdentifier;
    }

    /**
     * Update an item.
     *
     * @param string           $itemIdentifier The unique item identifier
     * @param string|int|array $key            The key to update, or an array of key-value pairs
     * @param mixed            $value          The value to set $key to
     *
     * @return void
     */
    public function update($itemIdentifier, $key, $value = null)
    {
        foreach ($this->contents() as $item) {
            if ($item->identifier == $itemIdentifier) {
                $item->update($key, $value);
                break;
            }
        }
    }

    /**
     * Remove an item from the basket.
     *
     * @param string $identifier Unique item identifier
     *
     * @return void
     */
    public function remove($identifier)
    {
        $this->store->remove($identifier);
    }

    /**
     * Destroy/empty the basket.
     *
     * @return void
     */
    public function destroy()
    {
        $this->store->destroy();
    }

    /**
     * Check if the basket has a specific item.
     *
     * @param string $itemIdentifier The unique item identifier
     *
     * @return bool Yes or no?
     */
    public function has($itemIdentifier)
    {
        return $this->store->has($itemIdentifier);
    }

    /**
     * Return a specific item object by identifier.
     *
     * @param string $itemIdentifier The unique item identifier
     *
     * @return Item Item object
     */
    public function item($itemIdentifier)
    {
        return $this->store->item($itemIdentifier);
    }

    /**
     * Returns the first occurance of an item with a given id.
     *
     * @param string $id The item id
     *
     * @return Item Item object
     */
    public function find($id)
    {
        return $this->store->find($id);
    }

    /**
     * The total tax value for the basket.
     *
     * @return float The total tax value
     */
    public function tax()
    {
        $total = 0;

        foreach ($this->contents() as $item) {
            $total += (float) $item->tax();
        }

        return $total;
    }

    /**
     * The total weight value for the basket.
     *
     * @return float The total weight value
     */
    public function weight()
    {
        $weight = 0;

        foreach ($this->contents() as $item) {
            $weight += (float) $item->weight();
        }

        return $weight;
    }

    /**
     * The total value of the basket.
     *
     * @param bool $includeTax Include tax on the total?
     *
     * @return float The total basket value
     */
    public function total($includeTax = true)
    {
        $total = 0;

        foreach ($this->contents() as $item) {
            $total += (float) $item->total($includeTax);
        }

        return (float) $total;
    }

    /**
     * The total number of items in the basket.
     *
     * @param bool $unique Just return unique items?
     *
     * @return int Total number of items
     */
    public function totalItems($unique = false)
    {
        $total = 0;

        foreach ($this->contents() as $item) {
            $total += $unique ? 1 : $item->quantity;
        }

        return $total;
    }

    /**
     * Set the basket identifier, useful if restoring a saved basket.
     *
     * @param  mixed The identifier
     *
     * @return void
     */
    public function setIdentifier($identifier)
    {
        $this->store->setIdentifier($identifier);
    }

    /**
     * Create a unique item identifier.
     *
     * @param array $item An array of item data
     *
     * @return string An md5 hash of item
     */
    protected function createItemIdentifier(array $item)
    {
        if (!array_key_exists('options', $item)) {
            $item['options'] = [];
        }

        ksort($item['options']);

        return md5($item['id'].serialize($item['options']));
    }

    /**
     * Check if a basket item has the required parameters.
     *
     * @param array $item An array of item data
     *
     * @return void
     */
    protected function checkArgs(array $item)
    {
        foreach ($this->requiredParams as $param) {
            if (!array_key_exists($param, $item)) {
                throw new InvalidArgumentException("The '{$param}' field is required");
            }
        }
    }
}
