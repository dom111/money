<?php

namespace Krixon\Money;

use Krixon\Math\Decimal;
use Krixon\Math\Ratio;

/**
 * A quotation of the relative value of one currency unit against another in the foreign exchange market.
 *
 * For example, "EUR/USD 1.2500" means that 1 EUR (the "base" currency) is exchanged for 1.25 USD (the "counter"
 * currency).
 *
 * @see http://en.wikipedia.org/wiki/Currency_pair
 */
class CurrencyPair
{
    const PRECISION = 4;
    
    /**6
     * @var Currency
     */
    private $counterCurrency;

    /**
     * @var Currency
     */
    private $baseCurrency;

    /**
     * @var Ratio
     */
    private $ratio;
    
    /**
     * @var Decimal
     */
    private $quote;


    public function __construct(Currency $baseCurrency, Currency $counterCurrency, Decimal $quote)
    {
        $this->baseCurrency    = $baseCurrency;
        $this->counterCurrency = $counterCurrency;
        $this->quote           = $quote->round(self::PRECISION);
        $this->ratio           = $this->quote->toRatio();
    }


    /**
     * Creates a new instance by parsing the ISO 4217 currency codes and a ratio.
     *
     * @param string $iso String representation of the form "EUR/USD 1.2500". The slash can be optionally be omitted.
     *
     * @throws \InvalidArgumentException
     * @return static
     */
    public static function fromIsoString($iso)
    {
        $pattern  = '#([A-Z]{3})/?([A-Z]{3}) (\d*\.?\d+)#';
        $matches  = [];
        
        if (!preg_match($pattern, $iso, $matches)) {
            throw new \InvalidArgumentException(
                "Cannot create CurrencyPair from ISO string '$iso': format of string is invalid."
            );
        }
        
        return new static(
            Currency::fromIsoCode($matches[1]),
            Currency::fromIsoCode($matches[2]),
            Decimal::fromString($matches[3])
        );
    }
    
    
    public static function fromRatio(Currency $baseCurrency, Currency $counterCurrency, Ratio $ratio)
    {
        return new static($baseCurrency, $counterCurrency, $ratio->toDecimal());
    }
    
    
    public static function fromDecimal(Currency $baseCurrency, Currency $counterCurrency, Decimal $quote)
    {
        return new static($baseCurrency, $counterCurrency, $quote);
    }
    
    
    /**
     * Converts Money with the base currency into a new instance using the counter currency.
     *
     * @param Money $money
     * @param int   $roundingMode
     *
     * @return Money
     */
    public function convert(Money $money, int $roundingMode = Money::ROUND_HALF_UP) : Money
    {
        if (!$this->containsCurrency($money->currency())) {
            throw new \InvalidArgumentException(sprintf(
                'Money cannot be converted as its Currency (%s) is not part of this CurrencyPair (%s).',
                $money->currency()->toString(),
                $this->toString()
            ));
        }
        
        $ratio    = $this->ratio;
        $currency = $this->baseCurrency;
        
        // If the Money uses the base currency rather than the counter, the Ratio needs to be inverted.
        // For example, given "GBP/USD 0.7500" the ratio of GBP to USD is 3:4. If we have USD this ratio is correct,
        // but if we have GBP the correct decimal multiplier is 1.3333 recurring (4:3).
        
        if ($money->isInCurrency($this->baseCurrency)) {
            $ratio    = $this->ratio->invert();
            $currency = $this->counterCurrency;
        }
        
        $quote = $ratio->toDecimal()->round(self::PRECISION);
        
        $newAmount = bcmul($money->amount(), $quote->toString(), Ratio::SCALE);
        $newAmount = (int)round($newAmount, 0, $roundingMode);
        
        return new Money($newAmount, $currency);
    }
    

    /**
     * @return Currency
     */
    public function counterCurrency() : Currency
    {
        return $this->counterCurrency;
    }
    

    /**
     * @return Currency
     */
    public function baseCurrency() : Currency
    {
        return $this->baseCurrency;
    }
    
    
    /**
     * Returns a two-element array containing the base and counter currencies (in that order).
     *
     * @return Currency[]
     */
    public function currencies() : array
    {
        return [$this->baseCurrency(), $this->counterCurrency()];
    }
    
    
    /**
     * @return Decimal
     */
    public function quote() : Decimal
    {
        return $this->quote;
    }
    
    
    /**
     * @return Ratio
     */
    public function ratio() : Ratio
    {
        return $this->ratio;
    }
    
    
    /**
     * @return string
     */
    public function toString() : string
    {
        return sprintf(
            '%s/%s @ %s (%s)',
            $this->baseCurrency->toString(),
            $this->counterCurrency->toString(),
            $this->quote->toString(self::PRECISION),
            $this->ratio->toString()
        );
    }
    
    
    /**
     * @return string
     */
    public function toIsoString() : string
    {
        return sprintf(
            '%s/%s %s',
            $this->baseCurrency->code(),
            $this->counterCurrency->code(),
            $this->quote->toString(self::PRECISION)
        );
    }
    
    
    /**
     * Determines if a Currency is part of this CurrencyPair.
     *
     * @param Currency $currency
     *
     * @return bool
     */
    public function containsCurrency(Currency $currency) : bool
    {
        return $currency->equals($this->baseCurrency()) || $currency->equals($this->counterCurrency());
    }
    
    
    /**
     * Determines if any of a set of Currency objects is part of this CurrencyPair.
     *
     * @param Currency[] $currencies
     *
     * @return bool
     */
    public function containsAnyOfCurrencies(Currency ...$currencies)
    {
        foreach ($currencies as $currency) {
            if ($this->containsCurrency($currency)) {
                return true;
            }
        }
        
        return false;
    }
}
