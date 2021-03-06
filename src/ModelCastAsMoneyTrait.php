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
    protected function castAsMoney($value, $currency = 'USD', $locale = 'en_US'): Money
    {
        return new Money($value, $currency, $locale);
    }

    /**
     * Convert the Money value back to a storable type.
     *
     * @return int
     */
    protected function castMoneyToInt($key, $value): int
    {
        if (is_object($value)) {
            return (int) $value->amount();
        }

        return (int) $value;
    }

    /**
     * Register the casting definitions.
     */
    public static function bootModelCastAsMoneyTrait()
    {
        static::registerCastFromDatabase('money', 'castAsMoney');
        static::registerCastToDatabase('money', 'castMoneyToInt');
        static::registerCastValidator('money', 'int');
    }
}