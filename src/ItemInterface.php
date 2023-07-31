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
 * Interface ItemInterface.
 *
 * @property string $id
 * @property string $identifier
 * @property int    $quantity
 * @property array  $options
 */
interface ItemInterface
{
    /**
     * @param string $identifier
     */
    public function setIdentifier(string $identifier): void;

    /**
     * Return itemIdentifier.
     *
     * @return string
     */
    public function getItemIdentifier(): string;

    /**
     * Return the total tax for this item.
     *
     * @return float
     */
    public function tax(): float;

    /**
     * Return the total price for this item.
     *
     * @param bool $includeTax
     *
     * @return float
     */
    public function total(bool $includeTax = true): float;

    /**
     * Return the quantity of the item.
     *
     * @return int
     */
    public function getQuantity(): int;

    /**
     * Set the quantity of the item.
     *
     * @param int $quantity
     *
     * @return void
     */
    public function setQuantity(int $quantity): void;

    /**
     * Return the total weight of the item.
     *
     * @return float
     */
    public function weight(): float;

    /**
     * Return the total of the item, with or without tax.
     *
     * @param bool $includeTax
     *
     * @return float
     */
    public function single(bool $includeTax = true): float;

    /**
     * Update a single key for this item, or multiple.
     *
     * @param mixed $key
     * @param mixed|null $value
     */
    public function update(mixed $key, mixed $value = null): void;

    /**
     * Check if this item has options.
     *
     * @return bool
     */
    public function hasOptions(): bool;

    /**
     * Convert the item into an array.
     *
     * @return array
     */
    public function toArray(): array;

    /**
     * Update data array using set magic method.
     *
     * @param mixed $param
     * @param mixed $value
     */
    public function __set(mixed $param, mixed $value): void;

    /**
     * Return the value of protected methods.
     *
     * @param string $param
     *
     * @return mixed
     */
    public function __get(string $param);
}
