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

namespace Lenius\Basket\Identifier;

use Lenius\Basket\IdentifierInterface;

/**
 * Class Cookie.
 */
class Cookie implements IdentifierInterface
{
    /**
     * Get the current or new unique identifier.
     *
     * @return string The identifier
     */
    public function get(): string
    {
        // Check if the 'cart_identifier' cookie exists and is a string
        if (isset($_COOKIE['cart_identifier']) && is_string($_COOKIE['cart_identifier'])) {
            return $_COOKIE['cart_identifier'];
        }

        return $this->regenerate();
    }

    /**
     * Regenerate the identifier.
     *
     * @return string The identifier
     */
    public function regenerate(): string
    {
        $identifier = md5(uniqid('', true));

        @setcookie('cart_identifier', $identifier, 0, '/');

        return $identifier;
    }

    /**
     * Forget the identifier.
     *
     * @return void
     */
    public function forget(): void
    {
        @setcookie('cart_identifier', '', time() - 3600);
    }
}
