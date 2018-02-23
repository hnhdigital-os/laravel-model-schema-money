<?php

declare(strict_types=1);

/*
 * This file is part of the Laravel Model Schema Money package.
 *
 * (c) H&H|Digital <hello@hnh.digital>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HnhDigital\ModelSchemaMoney;

/**
 * This is the Model Cast As Money trait.
 *
 * @author Rocco Howard <rocco@hnh.digital>
 */
trait ModelCastAsMoneyTrait
{
    /**
     * Cast value as Money.
     *
     * @param mixed $value
     *
     * @return Money
     */
    protected function asMoney($value, $currency = 'USD'): Money
    {
        return new Money($value, $currency);
    }

    /**
     * Convert the Money value back to a storable type.
     *
     * @return int
     */
    protected function castMoneyAttributeAsInt($key, $value): int
    {
        return (int) $value->amount();
    }

    /**
     * Register the casting definitions.
     */
    public static function bootModelCastAsMoneyTrait()
    {
        static::registerCastAs('money', 'asMoney');
        static::registerCastTo('money', 'castMoneyAttributeAsInt');
    }
}