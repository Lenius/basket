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

interface StorageInterface
{
    /**
     * Add or update an item in the cart.
     *
     * @param Item $item The item to insert or update
     *
     * @return void
     */
    public function insertUpdate(Item $item);

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
     * @return Item The item class
     */
    public function item($identifier);

    /**
     * Returns the first occurance of an item with a given id.
     *
     * @param string $id The item id
     *
     * @return Item Item object
     */
    public function find($id);

    /**
     * Remove an item from the cart.
     *
     * @param mixed $id
     *
     * @return void
     */
    public function remove($id);

    /**
     * Destroy the cart.
     *
     * @return void
     */
    public function destroy();

    /**
     * Set the cart identifier.
     *
     * @param string $identifier
     */
    public function setIdentifier($identifier);

    /**
     * Return the current cart identifier.
     *
     * @return void
     */
    public function getIdentifier();
}
