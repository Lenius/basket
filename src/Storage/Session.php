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
        session_id() || session_start();

        if (isset($_SESSION['cart'])) {
            static::$cart = unserialize($_SESSION['cart']);
        }
    }

    /**
     * The session store destructor.
     */
    public function __destruct()
    {
        $_SESSION['cart'] = serialize(static::$cart);
    }
}
