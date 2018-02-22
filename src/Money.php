<?php

declare(strict_types=1);

namespace HnhDigital\ModelSchemaMoney;

/*
 * This file is part of the Laravel Model Schema Money package.
 *
 * (c) H&H|Digital <hello@hnh.digital>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money as MoneyPhp;
use NumberFormatter;

/**
 * This is the Money class.
 *
 * @author Rocco Howard <rocco@hnh.digital>
 */
class Money
{
    /**
     * @var \Money\Money
     */
    private $money;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var string
     */
    private $language;

    /**
     * Constructor.
     * 
     * @param float  $amount
     * @param string $currency
     * @param string $language
     *
     * @return void
     */
    public function __construct($amount, string $currency = 'USD', string $language = 'en_US')
    {
        $this->money = new MoneyPhp($amount, new Currency($currency));
        $this->currency = $currency;
        $this->language = $language;
    }

    /**
     * Returns the value as a decimal value.
     * 
     * @return float
     */
    public function decimal(): float
    {
        return (float) (new DecimalMoneyFormatter(new ISOCurrencies()))->format($this->money);
    }

    /**
     * Returns thie value as a string.
     * 
     * @return string
     */
    public function format(): string
    {
        if (empty($this->language)) {
            return number_format($this->money);
        }

        $number_formatter = new NumberFormatter($this->language, NumberFormatter::CURRENCY);
        $money_formatter = new IntlMoneyFormatter($number_formatter, new ISOCurrencies());

        return $money_formatter->format($this->money);
    }

    /**
     * Return the raw value.
     *
     * @return string
     */
    public function amount(): string
    {
        return $this->money->getAmount();
    }

    /**
     * @param string $method
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call(string $method, array $arguments)
    {
        return call_user_func_array([$this->money, $method], $arguments);
    }

    /**
     * Returns the value as a string representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->format();
    }
}