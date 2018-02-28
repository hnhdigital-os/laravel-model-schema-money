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
use Symfony\Component\Intl\Intl;

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
    private $locale;

    /**
     * Constructor.
     * 
     * @param float  $amount
     * @param string $currency
     * @param string $locale
     *
     * @return void
     */
    public function __construct($amount, $currency = 'USD', string $locale = 'en_US')
    {
        if (is_string($currency)) {
            $currency = $currency ?: 'USD';
            $currency = new Currency($currency);
        }

        if ($amount instanceof self) {
            $amount = $amount->getAmount();
        }

        $locale = $locale ?: 'en_US';

        $this->money = new MoneyPhp($amount, $currency);
        $this->localize($currency, $locale);
    }

    /**
     * Get the currency.
     * 
     * @return $this
     */
    public function localize($currency = 'USD', string $locale = 'en_US'): self
    {
        if (is_string($currency)) {
            $currency = $currency ?: 'USD';
            $currency = new Currency($currency);
        }

        $locale = $locale ?: 'en_US';

        $this->money = new MoneyPhp($this->money->getAmount(), $currency);
        $this->currency = $currency;
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get the currency.
     *
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * Set the currency.
     *
     * @return $this
     */
    public function setCurrency($currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get the locale.
     *
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * Set the locale.
     *
     * @return $this
     */
    public function setLocale($locale): self
    {
        $this->locale = $locale;

        return $this;
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
        if (empty($this->locale)) {
            return number_format($this->money);
        }

        $number_formatter = new NumberFormatter($this->locale, NumberFormatter::CURRENCY);
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
     * Return the Money item.
     *
     * @return MoneyPhp
     */
    public function money(): MoneyPhp
    {
        return $this->money;
    }

    /**
     * Return the symbol value.
     *
     * @return string
     */
    public function symbol()
    {
        return Intl::getCurrencyBundle()->getCurrencySymbol($this->currency);
    }

    /**
     * @param string $method
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call(string $method, array $arguments)
    {
        // Always use the MoneyPhp object.
        foreach ($arguments as &$value) {
            if ($value instanceof self) {
                $value = $value->money();
            }
        }

        $result = call_user_func_array([$this->money, $method], $arguments);

        // Return new self, not instance of MoneyPhp.
        if ($result instanceof MoneyPhp) {
            $this->money = $result;
        }

        return $result;
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