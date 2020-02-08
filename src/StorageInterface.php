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
 * Interface StorageInterface.
 */
interface StorageInterface
{
    /**
     * Add or update an item in the cart.
     *
     * @param ItemInterface $item The item to insert or update
     */
    public function insertUpdate(ItemInterface $item);

    /**
     * Retrieve the cart data.
     *
     * @param bool $asArray
     *
     * @return array
     */
    public function &data($asArray = false);

    /**
     * Check if the item exists in the cart.
     *
     * @param mixed $identifier
     *
     * @return bool
     */
    public function has($identifier);

    /**
     * Get a single cart item by id.
     *
     * @param mixed $identifier The item id
     *
     * @return bool|Item
     */
    public function item($identifier);

    /**
     * Returns the first occurance of an item with a given id.
     *
     * @param string $id The item id
     *
     * @return bool|Item
     */
    public function find($id);

    /**
     * Remove an item from the cart.
     *
     * @param mixed $id
     */
    public function remove($id);

    /**
     * Destroy the cart.
     */
    public function destroy();

    /**
     * Restore the cart.
     */
    public function restore();

    /**
     * Set the cart identifier.
     *
     * @param string $identifier
     */
    public function setIdentifier($identifier);

    /**
     * Return the current cart identifier.
     *
     * @return string
     */
    public function getIdentifier();
}
