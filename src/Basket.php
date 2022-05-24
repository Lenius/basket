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

use InvalidArgumentException;

/**
 * Class Basket.
 */
class Basket
{
    /** @var string */
    protected string $id;

    /** @var IdentifierInterface */
    protected IdentifierInterface $identifier;

    /** @var StorageInterface */
    protected StorageInterface $store;

    /** @var array<string> */
    protected array $requiredParams = [
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
        $this->store->restore();

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
    public function &contents(bool $asArray = false): array
    {
        return $this->store->data($asArray);
    }

    /**
     * Insert an item into the basket.
     *
     * @param ItemInterface $item
     *
     * @return string A unique item identifier
     */
    public function insert(ItemInterface $item): string
    {
        $this->checkArgs($item);

        $itemIdentifier = $this->createItemIdentifier($item);

        if ($this->has($itemIdentifier) && $this->item($itemIdentifier) instanceof ItemInterface) {
            $item->setQuantity($this->item($itemIdentifier)->getQuantity() + $item->getQuantity());
            $this->update($itemIdentifier, $item);

            return $itemIdentifier;
        }

        $item->setIdentifier($itemIdentifier);

        $this->store->insertUpdate($item);

        return $itemIdentifier;
    }

    /**
     * Update an item.
     *
     * @param string $itemIdentifier
     * @param mixed $key
     * @param mixed $value
     */
    public function update(string $itemIdentifier, $key, $value = null): void
    {
        /** @var ItemInterface $item */
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
     */
    public function remove(string $identifier): void
    {
        $this->store->remove($identifier);
    }

    /**
     * Destroy/empty the basket.
     */
    public function destroy(): void
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
    public function has(string $itemIdentifier): bool
    {
        return $this->store->has($itemIdentifier);
    }

    /**
     * Return a specific item object by identifier.
     *
     * @param string $itemIdentifier The unique item identifier
     *
     * @return Item|bool
     */
    public function item(string $itemIdentifier)
    {
        return $this->store->item($itemIdentifier);
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
        return $this->store->find($id);
    }

    /**
     * The total tax value for the basket.
     *
     * @return float The total tax value
     */
    public function tax(): float
    {
        $total = 0;

        /** @var Item $item */
        foreach ($this->contents() as $item) {
            $total += $item->tax();
        }

        return (float) $total;
    }

    /**
     * The total weight value for the basket.
     *
     * @return float The total weight value
     */
    public function weight(): float
    {
        $weight = 0;

        /** @var Item $item */
        foreach ($this->contents() as $item) {
            $weight += $item->weight();
        }

        return (float) $weight;
    }

    /**
     * The total value of the basket.
     *
     * @param bool $includeTax Include tax on the total?
     *
     * @return float The total basket value
     */
    public function total(bool $includeTax = true): float
    {
        $total = 0;

        /** @var Item $item */
        foreach ($this->contents() as $item) {
            $total += $item->total($includeTax);
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
    public function totalItems(bool $unique = false): int
    {
        $total = 0;

        /** @var Item $item */
        foreach ($this->contents() as $item) {
            $total += $unique ? 1 : $item->getQuantity();
        }

        return $total;
    }

    /**
     * Set the basket identifier, useful if restoring a saved basket.
     *
     * @codeCoverageIgnore
     *
     * @param string $identifier
     */
    public function setIdentifier(string $identifier): void
    {
        $this->store->setIdentifier($identifier);
    }

    /**
     * Create a unique item identifier.
     *
     * @param ItemInterface $item
     *
     * @return string
     */
    protected function createItemIdentifier(ItemInterface $item): string
    {
        if (! array_key_exists('options', $item->toArray())) {
            $item->options = [];
        }

        $options = $item->options;

        ksort($options);

        $item->options = $options;

        return md5($item->id.serialize($item->options));
    }

    /**
     * Check if a basket item has the required parameters.
     * @param ItemInterface $item
     */
    protected function checkArgs(ItemInterface $item): void
    {
        foreach ($this->requiredParams as $param) {
            if (! array_key_exists($param, $item->toArray())) {
                throw new InvalidArgumentException("The '{$param}' field is required");
            }
        }
    }
}
