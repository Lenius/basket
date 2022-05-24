<?php

/**
 * This file is part of Lenius Basket, a PHP package to handle
 * your shopping basket.
 *
 * Copyright (c) 2022 Lenius.
 * https://github.com/lenius/basket
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Carsten Jonstrup<info@lenius.dk>
 * @copyright 2022 Lenius.
 *
 * @version production
 *
 * @see https://github.com/lenius/basket
 */

namespace Lenius\Basket;

/**
 * Interface StorageInterface.
 */
interface StorageInterface
{
    /**
     * Add or update an item in the cart.
     *
     * @param ItemInterface $item
     */
    public function insertUpdate(ItemInterface $item): void;

    /**
     * Retrieve the cart data.
     *
     * @param bool $asArray
     *
     * @return array
     */
    public function &data(bool $asArray = false): array;

    /**
     * Check if the item exists in the cart.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function has(string $identifier): bool;

    /**
     * Get a single cart item by id.
     *
     * @param string $identifier
     *
     * @return ItemInterface|bool
     */
    public function item(string $identifier): ItemInterface|bool;

    /**
     * Returns the first occurrence of an item with a given id.
     *
     * @param string $id
     *
     * @return ItemInterface|bool
     */
    public function find(string $id): ItemInterface|bool;

    /**
     * Remove an item from the cart.
     *
     * @param string $id
     */
    public function remove(string $id): void;

    /**
     * Destroy the cart.
     */
    public function destroy(): void;

    /**
     * Restore the cart.
     */
    public function restore(): void;

    /**
     * Set the cart identifier.
     *
     * @param string $identifier
     */
    public function setIdentifier(string $identifier): void;

    /**
     * Return the current cart identifier.
     *
     * @return string
     */
    public function getIdentifier(): string;
}
