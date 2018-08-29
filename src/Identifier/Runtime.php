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
 * @link https://github.com/lenius/basket
 */

namespace Lenius\Basket\Identifier;

use Lenius\Basket\IdentifierInterface;

class Runtime implements IdentifierInterface
{
    protected static $identifier;

    /**
     * Get the current or new unique identifier.
     *
     * @return string The identifier
     */
    public function get()
    {
        if (isset(static::$identifier)) {
            return static::$identifier;
        }

        return $this->regenerate();
    }

    /**
     * Regenerate the identifier.
     *
     * @return string The identifier
     */
    public function regenerate()
    {
        $identifier = md5(uniqid(null, true));

        static::$identifier = $identifier;

        return $identifier;
    }

    /**
     * Forget the identifier.
     *
     * @return void
     */
    public function forget()
    {
        static::$identifier = '';
    }
}
