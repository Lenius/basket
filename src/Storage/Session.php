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

use Lenius\Basket\StorageInterface;

/**
 * Class Session.
 */
class Session extends Runtime implements StorageInterface
{
    /**
     * The Session store constructor.
     */
    public function restore(): void
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if 'cart' exists in session and is a valid string
        if (isset($_SESSION['cart']) && is_string($_SESSION['cart'])) {
            $decodedCart = json_decode($_SESSION['cart'], true);

            // Validate the decoded JSON and ensure it's an array
            if (is_array($decodedCart)) {
                static::$cart = $decodedCart;

                return;
            }
        }

        // Fallback to an empty array if no valid cart data exists
        static::$cart = [];
    }

    /**
     * The session store destructor.
     */
    public function __destruct()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['cart'] = json_encode(static::$cart);
        }
    }
}
