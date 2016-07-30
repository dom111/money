<?php

namespace Krixon\Money;

use Krixon\Math\Decimal;
use Krixon\Math\Ratio;

/**
 * TODO: Support display of currency with the appropriate precision (10.50 USD, 525 JPY).
 *
 * The amount of precision used to store the amount of value depends on the currency.
 *
 * Amounts are always stored as integers to avoid rounding errors, but the same precision is required for all
 * currencies. In the case of a budgeting application like this, it's enough to store amounts to the smallest
 * value of the minor currency unit. To that end, we use the ISO4217 minor unit base 10 exponent to determine the
 * precision of the integer.
 *
 * For example, USD has an exponent of 2, so to represent an amount of $100 we expect the integer 10000. Japanese yen
 * has an exponent of 0, so to represent ¥100 we expect the integer 100.
 *
 * The exponents are available via the Currency class so it's always possible to convert to the correct precision
 * when constructing a Money.
 */
class Money
{
    const ROUND_HALF_UP   = PHP_ROUND_HALF_UP;
    const ROUND_HALF_DOWN = PHP_ROUND_HALF_DOWN;
    const ROUND_HALF_EVEN = PHP_ROUND_HALF_EVEN;
    const ROUND_HALF_ODD  = PHP_ROUND_HALF_ODD;
    const ROUNDING_MODES  = [
        self::ROUND_HALF_UP,
        self::ROUND_HALF_DOWN,
        self::ROUND_HALF_EVEN,
        self::ROUND_HALF_ODD,
    ];
    
    /**
     * @var int
     */
    private $amount;

    /**
     * @var Currency
     */
    private $currency;

    
    /**
     * The amount must be an integer with the correct minor unit digits of precision incorporated.
     *
     * For example:
     * USD has a minor unit exponent of 2, so there must be 2 minor units (1234567 == $12,345.67).
     * JPY has a minor unit exponent of 0, so there must be 0 minor units (1234567 == ¥123,456).
     * LYD has a minor unit exponent of 3, so there must be 3 minor units (1234567 == LD1,234.567).
     *
     * The largest exponent defined by ISO4217 is 3, so the maximum amount supported in a 64-bit system is
     * 9,223,372,036,854,775.807.
     *
     * @param int      $amount    The amount of value as an integer with self::PRECISION precision.
     * @param Currency $currency
     */
    public function __construct($amount, Currency $currency)
    {
        if (!is_int($amount)) {
            throw new \InvalidArgumentException(
                "Money's amount must a multiple of the currency's minor unit expressed as an integer"
            );
        }
        
        $this->amount   = $amount;
        $this->currency = $currency;
    }
    
    
    /**
     * Creates a new Money object with a value of 0.
     *
     * @param Currency $currency
     *
     * @return Money
     */
    public static function zero(Currency $currency) : Money
    {
        return new static(0, $currency);
    }
    
    
    /**
     * @param Money[] $addends
     *
     * @return Money
     */
    public static function sum(Money ...$addends) : Money
    {
        $current = current($addends);
        $result  = static::zero($current->currency());
        $amount  = 0;
        
        foreach ($addends as $addend) {
            $result->assertSameCurrencyAs($addend);
            $amount += $addend->amount;
        }
        
        $result->amount = $amount;
        
        return $result;
    }
    
    
    /**
     * Creates a new instance from a 2-element array containing the major and minor unit amounts.
     *
     * For convenience the minor unit amount can be omitted for currencies which do not support minor units.
     *
     * For example:
     * Money::fromArray([125, 50],  new Currency('USD')); // US$125.50
     * Money::fromArray([1999, 0],  new Currency('JPY')); // ¥1999
     * Money::fromArray([1999],     new Currency('JPY')); // ¥1999
     * Money::fromArray([999, 455], new Currency('LYD')); // LYD999.455
     *
     * @param array    $amount
     * @param Currency $currency
     *
     * @return Money
     */
    public static function fromArray(array $amount, Currency $currency) : Money
    {
        $majorUnitAmount = array_shift($amount);
        $minorUnitAmount = empty($amount) ? 0 : array_shift($amount);
        
        return static::fromMajorAndMinorUnitAmounts($majorUnitAmount, $minorUnitAmount, $currency);
    }
    
    
    /**
     * Creates a new instance from a Decimal.
     *
     * @param Decimal  $decimal
     * @param Currency $currency
     *
     * @return Money
     */
    public static function fromDecimal(Decimal $decimal, Currency $currency)
    {
        return static::fromDecimalString($decimal->toString(), $currency);
    }
    
    
    /**
     * Creates a new instance from a decimal string.
     *
     * For example:
     * Money::fromDecimalString('125.50', Currency::USD()); // US$125.50
     * Money::fromDecimalString('1999', Currency::JPY());   // ¥1999
     *
     * @param string      $string
     * @param Currency    $currency
     * @param string|null $locale
     *
     * @return Money
     */
    public static function fromDecimalString(string $string, Currency $currency, string $locale = null) : Money
    {
        $locale = self::resolveLocale($locale);
    
        $formatter = \NumberFormatter::create($locale, \NumberFormatter::DECIMAL);
        
        if (false === ($result = $formatter->parse($string))) {
            throw new \InvalidArgumentException(
                "Cannot parse decimal string '$string' into Money using locale '$locale'."
            );
        }
        
        return new static((int)($result * $currency->minorUnitMultiplier()), $currency);
    }
    
    
    /**
     * Creates a new instance from a currency string.
     *
     * For example:
     * Money::fromDecimalString('$125.50'); // US$125.50
     * Money::fromDecimalString('¥1999');   // ¥1999
     *
     * @param string      $string
     * @param string|null $locale
     *
     * @return Money
     */
    public static function fromCurrencyString(string $string, string $locale = null): Money
    {
        $locale = self::resolveLocale($locale);
        
        $formatter    = \NumberFormatter::create($locale, \NumberFormatter::CURRENCY);
        $currency     = null;
        $parsedAmount = $formatter->parseCurrency($string, $currency);
        
        if (false === $parsedAmount) {
            throw new \InvalidArgumentException(
                "Cannot parse currency string '$string' into Money using locale '$locale'"
            );
        }
        
        $currency = new Currency($currency);
        
        return new static((int)($parsedAmount * $currency->minorUnitMultiplier()), $currency);
    }
    
    
    /**
     * Creates a new instance using the specified major and minor unit amounts.
     *
     * @param int      $majorUnitAmount
     * @param int      $minorUnitAmount
     * @param Currency $currency
     *
     * @return Money
     */
    public static function fromMajorAndMinorUnitAmounts(
        int $majorUnitAmount,
        int $minorUnitAmount,
        Currency $currency
    ) : Money {
        
        $exponent        = $currency->minorUnitExponent();
        $minorUnitAmount = str_pad((string)$minorUnitAmount, $exponent, '0', STR_PAD_RIGHT);
        
        if (0 === $exponent && 0 === (int)$minorUnitAmount) {
            $minorUnitAmount = '';
        }
        
        $minorUnitDigitCount = strlen($minorUnitAmount);
        
        if ($minorUnitDigitCount > $exponent) {
            throw new Exception\InvalidAmountException(sprintf(
                'Currency %s accepts a maximum of %d minor unit digits, but got %d (%d.%d).',
                $currency->toString(),
                $exponent,
                $minorUnitDigitCount,
                $majorUnitAmount,
                $minorUnitAmount
            ));
        }
        
        return new static((int)($majorUnitAmount . $minorUnitAmount), $currency);
    }
    
    
    /**
     * The amount of Money in the smallest units of the Money's Currency (eg pence for GBP, cents for USD).
     *
     * @return int
     */
    public function amount() : int
    {
        return $this->amount;
    }

    
    /**
     * The currency in which this money is valued.
     *
     * @return Currency
     */
    public function currency() : Currency
    {
        return $this->currency;
    }
    
    
    /**
     * Determines if a Money instance is in the same currency as this instance.
     *
     * @param Money $other
     *
     * @return bool
     */
    public function isSameCurrencyAs(Money $other) : bool
    {
        return $this->currency->equals($other->currency);
    }
    
    
    /**
     * Determines if this instance is in a specified currency.
     *
     * @param Currency $currency
     *
     * @return bool
     */
    public function isInCurrency(Currency $currency) : bool
    {
        return $this->currency->equals($currency);
    }
    
    
    /**
     * Determines if a Money instance is equal to this instance.
     *
     * Two instances are equal if they represent the same amount in the same currency.
     *
     * @param Money $other
     *
     * @return bool
     */
    public function equals(Money $other) : bool
    {
        return $this->amount === $other->amount && $this->isSameCurrencyAs($other);
    }
    
    
    /**
     * Determines if a Money instance has a greater value than this instance.
     *
     * @param Money $other
     *
     * @return bool
     */
    public function isGreaterThan(Money $other) : bool
    {
        return 1 === $this->compare($other);
    }
    
    
    /**
     * Determines if a Money instance has a greater or equal value than this instance.
     *
     * @param Money $other
     *
     * @return bool
     */
    public function isGreaterThanOrEqualTo(Money $other) : bool
    {
        return 0 <= $this->compare($other);
    }
    
    
    /**
     * Determines if a Money instance has a lower value than this instance.
     *
     * @param Money $other
     *
     * @return bool
     */
    public function isLessThan(Money $other) : bool
    {
        return -1 === $this->compare($other);
    }
    
    
    /**
     * Determines if a Money instance has a lower or equal value than this instance.
     *
     * @param Money $other
     *
     * @return bool
     */
    public function isLessThanOrEqualTo(Money $other) : bool
    {
        return 0 >= $this->compare($other);
    }
    
    
    /**
     * Determines if this instance has a zero value.
     *
     * @return bool
     */
    public function isZero() : bool
    {
        return 0 === $this->amount;
    }
    
    
    /**
     * Determines if this instance has a positive value.
     *
     * Note that 0 is not considered a positive or negative value.
     *
     * @return bool
     */
    public function isPositive() : bool
    {
        return $this->amount > 0;
    }
    
    
    /**
     * Determines if this instance has a negative value.
     *
     * Note that 0 is not considered a positive or negative value.
     *
     * @return bool
     */
    public function isNegative() : bool
    {
        return $this->amount < 0;
    }
    
    
    /**
     * Compares this instance with another.
     *
     * @param Money $other
     *
     * @return int An integer less than, equal to or greater than 0 when this instance is respectively less than,
     *             equal to or greater than the $other instance.
     */
    public function compare(Money $other) : int
    {
        $this->assertSameCurrencyAs($other);
        
        return $this->amount <=> $other->amount;
    }
    
    
    /**
     * Adds one or more money instances to this instance.
     *
     * Every addend must be in the same currency as this one.
     *
     * @param Money[] $addends
     *
     * @return Money
     */
    public function add(Money ...$addends) : Money
    {
        if (empty($addends)) {
            return $this;
        }
        
        $addends[] = $this;
    
        return static::sum(...$addends);
    }
    
    
    public function subtract(Money $subtrahend)
    {
        $this->assertSameCurrencyAs($subtrahend);
        
        return new static($this->amount - $subtrahend->amount, $this->currency);
    }
    
    
    /**
     * @param int|float $multiplier
     * @param int       $roundingMode
     *
     * @return static
     */
    public function multiply($multiplier, $roundingMode = self::ROUND_HALF_UP)
    {
        self::assertValidOperand($multiplier);
        self::assertValidRoundingMode($roundingMode);
        
        $product = (int)round($this->amount * $multiplier, 0, $roundingMode);
        
        return new static($product, $this->currency);
    }
    
    
    /**
     * @param int|float $divisor
     * @param int       $roundingMode
     *
     * @return static
     */
    public function divide($divisor, $roundingMode = self::ROUND_HALF_UP)
    {
        if ($divisor === 0) {
            throw new \InvalidArgumentException('Divisor cannot be 0.');
        }
    
        self::assertValidOperand($divisor);
        self::assertValidRoundingMode($roundingMode);
        
        $quotient = (int)round($this->amount / $divisor, 0, $roundingMode);
        
        return new static($quotient, $this->currency);
    }
    
    
    /**
     * Allocate the value according to a list of ratios.
     *
     * For example, given an amount of 200 and ratios of [0.25, 0.25, 0.5], this would result in an array of 3 Money
     * instances with amounts 50, 50 and 100 respectively.
     *
     * Any remainder after distributing the amount according to the ratios will be distributed amongst the results
     * equally, starting from the first result. For example, given an amount of 100 and ratios of [0.33, 0.33, 0.33],
     * the result of sharing the amount is [33, 33, 33], leaving a remainder of 1. This is added to the first
     * amount, giving a final result of [34, 33, 33].
     *
     * The total amount will always be allocated to the resulting Moneys.
     *
     * Array keys are maintained, i.e. the resulting array will have keys corresponding those of the ratios.
     *
     * @param Ratio[] $ratios
     *
     * @return static[]
     */
    public function allocate(Ratio ...$ratios)
    {
        if (empty($ratios)) {
            return [];
        }
        
        $remainder = $this->amount;
        $results   = [];
        
        $ratios = array_map(function (Ratio $ratio) {
            return $ratio->toFloat();
        }, $ratios);
        
        $total = array_sum($ratios);
        
        // Allocate the amount according to the input ratios.
        foreach ($ratios as $ratio) {
            $share      = (int)floor($this->amount * $ratio / $total);
            $results[]  = new static($share, $this->currency);
            $remainder -= $share;
        }
        
        // Distribute any remainder, starting at the first result.
        for ($i = 0; $remainder > 0; $i++) {
            ++$results[$i]->amount;
            --$remainder;
        }
        
        return $results;
    }
    
    
    public function toCurrencyString($locale = null)
    {
        $locale = self::resolveLocale($locale);
        
        $formatter = \NumberFormatter::create($locale, \NumberFormatter::CURRENCY);
        $decimal   = bcdiv($this->amount, $this->currency->minorUnitMultiplier(), $this->currency->minorUnitExponent());
        
        return $formatter->formatCurrency($decimal, $this->currency->code());
    }
    
    
    public function toDecimalString($locale = null)
    {
        $locale = self::resolveLocale($locale);
        
        $formatter = \NumberFormatter::create($locale, \NumberFormatter::DECIMAL);
        $decimal   = bcdiv($this->amount, $this->currency->minorUnitMultiplier(), $this->currency->minorUnitExponent());
        
        return $formatter->format($decimal);
    }
    
    
    /**
     * Takes a locale or null and returns a locale.
     *
     * If locale is null then the default locale is returned.
     *
     * @param string|null $locale
     *
     * @return string
     */
    private static function resolveLocale(string $locale = null) : string
    {
        if (null === $locale) {
            $locale = \Locale::getDefault();
            
            return $locale;
        }
        
        return $locale;
    }
    
    
    /**
     * @param Money $other
     */
    private function assertSameCurrencyAs(Money $other)
    {
        if (!$this->isSameCurrencyAs($other)) {
            throw Exception\IllegalCurrencyException::incompatibleMoneyCurrencies(
                $this->currency(),
                $other->currency()
            );
        }
    }
    
    
    /**
     * @param $operand
     */
    private static function assertValidOperand($operand)
    {
        if (!is_int($operand) && !is_float($operand)) {
            throw new \InvalidArgumentException('Operand should be an integer or a float.');
        }
    }
    
    
    /**
     * @param $roundingMode
     */
    private static function assertValidRoundingMode($roundingMode)
    {
        if (!in_array($roundingMode, self::ROUNDING_MODES, true)) {
            throw new \InvalidArgumentException(
                'Rounding mode should be one of Money::[ROUND_HALF_DOWN | ROUND_HALF_EVEN | ROUND_HALF_ODD | ' .
                'ROUND_HALF_UP]'
            );
        }
    }
}
