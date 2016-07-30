<?php

namespace Krixon\Money\Test;

use Krixon\Math\Ratio;
use Krixon\Money\Currency;
use Krixon\Money\Exception\IllegalCurrencyException;
use Krixon\Money\Money;
use Krixon\Money\Exception\InvalidAmountException;

/**
 * Unit tests for the Money class.
 */
class MoneyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $defaultLocale;
    
    
    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        
        // Store the default locale so it can be reset after running the test (see tearDown()).
        $this->defaultLocale = \Locale::getDefault();
    }
    
    
    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        parent::tearDown();
        
        // Restore the default locale.
        \Locale::setDefault($this->defaultLocale);
    }
    
    
    /**
     * @dataProvider validAmountDecimalStringProvider
     *
     * @param string      $currency
     * @param string      $amount
     * @param int         $expected
     * @param string|null $locale
     */
    public function testCanInstantiateFromDecimalString(
        string $currency,
        string $amount,
        int $expected,
        string $locale = null
    ) {
        $money = Money::fromDecimalString($amount, new Currency($currency), $locale);
        
        $this->assertSame($expected, $money->amount());
    }
    
    
    public function validAmountDecimalStringProvider() : array
    {
        // TODO: Test different locales.
        
        return [
            ['USD', '123,456.2', 12345620],
            ['USD', '123,456.78', 12345678],
            ['USD', '123,456', 12345600],
            ['USD', '512.1', 51210],
            ['USD', '-999999.99', -99999999],
            ['LYD', '123,456.789', 123456789],
            ['LYD', '123,456.7', 123456700],
            ['LYD', '123,456', 123456000],
            ['LYD', '666,555.444', 666555444],
            ['LYD', '-987654.12', -987654120],
            ['JPY', '123,456', 123456],
            ['EGP', '١٢٣٤٥٦٫٧٨٩', 123456789, 'ar_EG'],
        ];
    }
    
    
    /**
     * @dataProvider validAmountCurrencyStringProvider
     *
     * @param string      $amount
     * @param int         $expectedAmount
     * @param string      $expectedCurrency
     * @param string|null $locale
     */
    public function testCanInstantiateFromCurrencyString(
        string $amount,
        int $expectedAmount,
        string $expectedCurrency,
        string $locale = null
    ) {
        $money = Money::fromCurrencyString($amount, $locale);
        
        $this->assertSame($expectedAmount, $money->amount());
        $this->assertSame($expectedCurrency, $money->currency()->code());
    }
    
    
    public function validAmountCurrencyStringProvider() : array
    {
        // TODO: Test different locales.
        
        return [
            ['$123,456.2', 12345620, 'USD'],
            ['$123,456.78', 12345678, 'USD'],
            ['$123,456', 12345600, 'USD'],
            ['$512.1', 51210, 'USD'],
            ['-$999999.99', -99999999, 'USD'],
            ['LYD123,456.789', 123456789, 'LYD'],
            ['LYD123,456.7', 123456700, 'LYD'],
            ['LYD123,456', 123456000, 'LYD'],
            ['LYD666,555.444', 666555444, 'LYD'],
            ['-¥987654', -987654, 'JPY'],
            ['¥123,456', 123456, 'JPY'],
            ['£150', 15000, 'GBP', 'en_GB'],
            ['EGP150', 150000, 'EGP', 'en_GB'],
            ['$512.1', 51210, 'USD', 'en_GB'],
            ['150,25 €', 15025, 'EUR', 'de_DE'],
            ['₹ 99,99,999.00', 999999900, 'INR', 'en_IN'],
        ];
    }
    
    
    /**
     * @dataProvider validAmountArrayProvider
     *
     * @param string $currency
     * @param array  $amount
     * @param int    $expected
     *
     * @return void
     */
    public function testCanInstantiateFromArray($currency, array $amount, $expected)
    {
        $money = Money::fromArray($amount, new Currency($currency));
        
        $this->assertSame($expected, $money->amount());
    }
    
    
    public function validAmountArrayProvider() : array
    {
        return [
            ['USD', [123456, 78], 12345678],
            ['USD', [123456], 12345600],
            ['USD', [512, 1], 51210],
            ['USD', [-999999, 99], -99999999],
            ['LYD', [123456, 789], 123456789],
            ['LYD', [123456, 7], 123456700],
            ['LYD', [123456], 123456000],
            ['LYD', [666555, 444], 666555444],
            ['LYD', [-987654, 12], -987654120],
            ['JPY', [123456], 123456],
            ['JPY', [123456, 0], 123456],
        ];
    }
    
    
    /**
     * @dataProvider invalidMinorUnitValuesProvider
     *
     * @param string $currency
     * @param int    $minorAmount
     *
     * @return void
     */
    public function testCannotInstantiateUsingMoreMinorUnitDigitsThanCurrencyAllows($currency, $minorAmount)
    {
        $this->expectException(InvalidAmountException::class);
        
        Money::fromMajorAndMinorUnitAmounts(100, $minorAmount, new Currency($currency));
    }
    
    
    public function invalidMinorUnitValuesProvider() : array
    {
        return [
            ['JPY', 1],    // JPY allows 0 minor unit digits.
            ['USD', 123],  // USD allows 2 minor unit digits.
            ['LYD', 1234], // LYD allows 3 minor unit digits.
        ];
    }
    
    
    public function testCannotInstantiateWithNonIntegerValue()
    {
        $this->expectException(\InvalidArgumentException::class);
        
        new Money(123.45, Currency::XTS());
    }
    
    
    /**
     * @dataProvider invalidDecimalStringProvider
     *
     * @param mixed $invalid
     */
    public function testCannotInstantiateUsingInvalidDecimalString($invalid)
    {
        $this->expectException(\InvalidArgumentException::class);
        
        Money::fromDecimalString($invalid, Currency::XTS());
    }
    
    
    public function invalidDecimalStringProvider() : array
    {
        return [
            ['foobar'],
            [false], // Type coercion changes this to an empty string.
        ];
    }
    
    
    /**
     * Tests that non-string arguments cause a TypeError.
     *
     * Arguments which can be coerced to strings will not fail this test as strict mode is disabled.
     *
     * @dataProvider nonStringDecimalStringProvider
     *
     * @param mixed $invalid
     */
    public function testCannotInstantiateUsingNonStringArgument($invalid)
    {
        $this->expectException(\TypeError::class);
        
        Money::fromDecimalString($invalid, Currency::XTS());
    }
    
    
    public function nonStringDecimalStringProvider() : array
    {
        return [
            [new \stdClass],
            [[1, 2, 3]],
        ];
    }
    
    
    /**
     * @dataProvider validRenderedStringProvider
     *
     * @param string $money
     * @param string $currency
     * @param string $expected
     * @param string $locale
     */
    public function testCanRenderToString(string $money, string $currency, string $expected, string $locale)
    {
        $money = Money::fromDecimalString($money, new Currency($currency), 'en_GB');
        
        $this->assertSame($expected, $money->toCurrencyString($locale));
    }
    
    
    public function validRenderedStringProvider() : array
    {
        return [
            ['150.250', 'USD', '$150.25', 'en'],
            ['150.250', 'USD', '$150.25', 'en_US'],
            ['150.250', 'USD', 'US$150.25', 'en_GB'],
            ['150.250', 'USD', '150,25 $US', 'fr_FR'],
            ['150.250', 'GBP', '£150.25', 'en'],
            ['150.250', 'LYD', 'LYD150.250', 'en'],
            ['150.250', 'USD', '150,25 $', 'de_DE'],
            ['150.250', 'EUR', '150,25 €', 'de_DE'],
            ['1,999,999', 'INR', '₹ 19,99,999.00', 'en_IN'],
        ];
    }
    
    
    public function testRenderingToStringUsesDefaultLocaleIfNoneSet()
    {
        $money = new Money(10000, Currency::GBP());
        
        \Locale::setDefault('en_GB');
        
        $this->assertSame('£100.00', $money->toCurrencyString());
        
        \Locale::setDefault('fr_FR');
        
        $this->assertSame('100,00 £GB', $money->toCurrencyString());
    }
    
    
    /**
     * @dataProvider validRenderedDecimalStringProvider
     *
     * @param string $money
     * @param string $currency
     * @param string $expected
     * @param string $locale
     */
    public function testCanRenderToDecimalString(string $money, string $currency, string $expected, string $locale)
    {
        $money = Money::fromDecimalString($money, new Currency($currency), 'en_GB');
        
        $this->assertSame($expected, $money->toDecimalString($locale));
    }
    
    
    public function validRenderedDecimalStringProvider() : array
    {
        return [
            ['150.250', 'USD', '150.25', 'en'],
            ['150.250', 'USD', '150.25', 'en_US'],
            ['150.250', 'USD', '150.25', 'en_GB'],
            ['150.250', 'USD', '150,25', 'fr_FR'],
            ['150.250', 'GBP', '150.25', 'en'],
            ['150.250', 'LYD', '150.25', 'en'],
            ['150.250', 'USD', '150,25', 'de_DE'],
            ['150.250', 'EUR', '150,25', 'de_DE'],
            ['1,999,999', 'INR', '19,99,999', 'en_IN'],
        ];
    }
    
    
    public function testRenderingToDecimalStringUsesDefaultLocaleIfNoneSet()
    {
        $money = new Money(10050, Currency::GBP());
        
        \Locale::setDefault('en_GB');
        
        $this->assertSame('100.5', $money->toDecimalString());
        
        \Locale::setDefault('fr_FR');
        
        $this->assertSame('100,5', $money->toDecimalString());
    }
    
    
    /**
     * @dataProvider sameCurrencyProvider
     *
     * @param string $a
     * @param string $b
     * @param bool   $expected
     *
     * @return void
     */
    public function testCanDetermineIfInstancesAreInSameCurrency(string $a, string $b, $expected)
    {
        $a = Money::fromDecimalString('100', Currency::fromIsoCode($a));
        $b = Money::fromDecimalString('100', Currency::fromIsoCode($b));
        
        $this->assertSame($expected, $a->isSameCurrencyAs($b));
        $this->assertSame($expected, $b->isSameCurrencyAs($a));
    }
    
    
    public function sameCurrencyProvider() : array
    {
        return [
            ['GBP', 'GBP', true],
            ['GBP', 'USD', false],
        ];
    }
    
    
    /**
     * @dataProvider equalInstancesProvider
     *
     * @param Money $a
     * @param Money $b
     * @param bool  $expected
     *
     * @return void
     */
    public function testCanDetermineIfInstancesAreEqual(Money $a, Money $b, $expected)
    {
        $this->assertSame($expected, $a->equals($b));
        $this->assertSame($expected, $b->equals($a));
    }
    
    
    public function equalInstancesProvider()
    {
        return [
            [
                Money::fromDecimalString('100', Currency::USD()),
                Money::fromDecimalString('100', Currency::USD()),
                true,
            ],
            [
                Money::fromDecimalString('50', Currency::USD()),
                Money::fromDecimalString('100', Currency::USD()),
                false,
            ],
            [
                Money::fromDecimalString('100', Currency::USD()),
                Money::fromDecimalString('100', Currency::GBP()),
                false,
            ],
            [
                Money::fromDecimalString('50', Currency::USD()),
                Money::fromDecimalString('100', Currency::GBP()),
                false,
            ],
        ];
    }
    
    
    /**
     * @dataProvider greaterThanProvider
     *
     * @param string $a
     * @param string $b
     * @param bool   $expected
     *
     * @return void
     */
    public function testCanDetermineIfInstanceIsGreaterThanOtherInstance($a, $b, $expected)
    {
        $a = Money::fromDecimalString($a, Currency::GBP());
        $b = Money::fromDecimalString($b, Currency::GBP());
        
        $this->assertSame($expected, $a->isGreaterThan($b));
    }
    
    
    public function greaterThanProvider()
    {
        return [
            ['100', '200', false],
            ['100.50', '100.51', false],
            ['100', '100', false],
            ['0', '100', false],
            ['100.01', '100', true],
            ['200', '100', true],
            ['200', '0', true],
        ];
    }
    
    
    /**
     * @dataProvider greaterThanOrEqualToProvider
     *
     * @param string $a
     * @param string $b
     * @param bool   $expected
     */
    public function testCanDetermineIfInstanceIsGreaterThanOrEqualToOtherInstance($a, $b, $expected)
    {
        $a = Money::fromDecimalString($a, Currency::GBP());
        $b = Money::fromDecimalString($b, Currency::GBP());
        
        $this->assertSame($expected, $a->isGreaterThanOrEqualTo($b));
    }
    
    
    public function greaterThanOrEqualToProvider()
    {
        return [
            ['100', '200', false],
            ['100.50', '100.51', false],
            ['100', '100', true],
            ['0', '100', false],
            ['100.01', '100', true],
            ['200', '100', true],
            ['200', '0', true],
        ];
    }
    
    
    /**
     * @dataProvider lessThanProvider
     *
     * @param string $a
     * @param string $b
     * @param bool   $expected
     */
    public function testCanDetermineIfInstanceIsLessThanOtherInstance($a, $b, $expected)
    {
        $a = Money::fromDecimalString($a, Currency::GBP());
        $b = Money::fromDecimalString($b, Currency::GBP());
        
        $this->assertSame($expected, $a->isLessThan($b));
    }
    
    
    public function lessThanProvider()
    {
        return [
            ['100', '200', true],
            ['100.50', '100.51', true],
            ['100', '100', false],
            ['0', '100', true],
            ['100.01', '100', false],
            ['200', '100', false],
            ['200', '0', false],
        ];
    }
    
    
    /**
     * @dataProvider lessThanOrEqualToProvider
     *
     * @param string $a
     * @param string $b
     * @param bool   $expected
     */
    public function testCanDetermineIfInstanceIsLessThanOrEqualToOtherInstance($a, $b, $expected)
    {
        $a = Money::fromDecimalString($a, Currency::GBP());
        $b = Money::fromDecimalString($b, Currency::GBP());
        
        $this->assertSame($expected, $a->isLessThanOrEqualTo($b));
    }
    
    
    public function lessThanOrEqualToProvider()
    {
        return [
            ['100', '200', true],
            ['100.50', '100.51', true],
            ['100', '100', true],
            ['0', '100', true],
            ['100.01', '100', false],
            ['200', '100', false],
            ['200', '0', false],
        ];
    }
    
    
    public function testCanDetermineIfZero()
    {
        $money = Money::fromDecimalString('1', Currency::GBP());
        $this->assertFalse($money->isZero());
        
        $money = Money::fromDecimalString('-1', Currency::GBP());
        $this->assertFalse($money->isZero());
        
        $money = Money::fromDecimalString('0', Currency::GBP());
        $this->assertTrue($money->isZero());
    }
    
    
    public function testCanDetermineIfPositive()
    {
        $money = Money::fromDecimalString('1', Currency::GBP());
        $this->assertTrue($money->isPositive());
        
        $money = Money::fromDecimalString('-1', Currency::GBP());
        $this->assertFalse($money->isPositive());
        
        $money = Money::fromDecimalString('0', Currency::GBP());
        $this->assertFalse($money->isPositive());
    }
    
    
    public function testCanDetermineIfNegative()
    {
        $money = Money::fromDecimalString('1', Currency::GBP());
        $this->assertFalse($money->isNegative());
        
        $money = Money::fromDecimalString('-1', Currency::GBP());
        $this->assertTrue($money->isNegative());
        
        $money = Money::fromDecimalString('0', Currency::GBP());
        $this->assertFalse($money->isNegative());
    }
    
    
    public function testCannotCompareInstancesWithDifferentCurrencies()
    {
        $this->expectException(IllegalCurrencyException::class);
        $this->expectExceptionCode(IllegalCurrencyException::INCOMPATIBLE_MONEY_CURRENCIES);
        
        $a = Money::fromDecimalString('100', Currency::GBP());
        $b = Money::fromDecimalString('200', Currency::USD());
        
        $a->compare($b);
    }
    
    
    /**
     * @dataProvider additionProvider
     *
     * @param int $a
     * @param int $b
     * @param int $expected
     *
     * @return void
     */
    public function testCanAddInstance($a, $b, $expected)
    {
        $a = new Money($a, Currency::GBP());
        $b = new Money($b, Currency::GBP());
        
        $total = $a->add($b);
        
        $this->assertSame($expected, $total->amount());
    }
    
    
    public function additionProvider()
    {
        return [
            [10000, 10000, 20000],
            [15000, 10000, 25000],
            [-10000, 10000, 0],
            [-10000, 5000, -5000],
        ];
    }
    
    
    /**
     * @dataProvider subtractionProvider
     *
     * @param int $a
     * @param int $b
     * @param int $expected
     *
     * @return void
     */
    public function testCanSubtractInstance($a, $b, $expected)
    {
        $a = new Money($a, Currency::GBP());
        $b = new Money($b, Currency::GBP());
        
        $total = $a->subtract($b);
        
        $this->assertSame($expected, $total->amount());
    }
    
    
    public function subtractionProvider()
    {
        return [
            [10000, 10000, 0],
            [15000, 10000, 5000],
            [-10000, 10000, -20000],
            [-10000, 5000, -15000],
            [10000, -5000, 15000],
        ];
    }
    
    
    /**
     * @dataProvider multiplicationProvider
     *
     * @param int       $money
     * @param int|float $multiplier
     * @param int       $expected
     * @param int       $roundingMode
     *
     * @internal     param int $b
     * @return void
     */
    public function testCanMultiply($money, $multiplier, $expected, $roundingMode = Money::ROUND_HALF_UP)
    {
        $money = new Money($money, Currency::GBP());
        
        $this->assertSame($expected, $money->multiply($multiplier, $roundingMode)->amount());
    }
    
    
    public function multiplicationProvider()
    {
        // TODO: Test rounding modes.
        
        return [
            [10000, 2, 20000],
            [10000, 5, 50000],
            [10000, -1, -10000],
        ];
    }
    
    
    /**
     * @dataProvider invalidMultiplierProvider
     *
     * @param mixed $multiplier
     */
    public function testCannotMultiplyWithInvalidMultiplier($multiplier)
    {
        $this->expectException(\InvalidArgumentException::class);
        
        (new Money(10000, Currency::GBP()))->multiply($multiplier);
    }
    
    
    public function invalidMultiplierProvider()
    {
        return [
            ['string'],
            [true],
            [false],
            [null],
            [new \stdClass],
        ];
    }
    
    
    /**
     * @dataProvider divisionProvider
     *
     * @param int       $money
     * @param int|float $divisor
     * @param int       $expected
     * @param int       $roundingMode
     *
     * @internal     param int $b
     * @return void
     */
    public function testCanDivide($money, $divisor, $expected, $roundingMode = Money::ROUND_HALF_UP)
    {
        $money = new Money($money, Currency::GBP());
        
        $this->assertSame($expected, $money->divide($divisor, $roundingMode)->amount());
    }
    
    
    public function divisionProvider()
    {
        // TODO: Test rounding modes.
        
        return [
            [10000, 2, 5000],
            [10000, 5, 2000],
            [10000, -1, -10000],
        ];
    }
    
    
    /**
     * @dataProvider invalidDivisorProvider
     *
     * @param mixed $divisor
     */
    public function testCannotDivideWithInvalidDivisor($divisor)
    {
        $this->expectException(\InvalidArgumentException::class);
        
        (new Money(10000, Currency::GBP()))->divide($divisor);
    }
    
    
    public function invalidDivisorProvider()
    {
        return array_merge($this->invalidMultiplierProvider(), [[0]]);
    }
    
    
    /**
     * @dataProvider allocateByRatiosProvider
     *
     * @param int      $a
     * @param string[] $ratios
     * @param int[]    $expected
     *
     * @return void
     */
    public function testCanAllocateByRatios($a, array $ratios, array $expected)
    {
        $a       = new Money($a, Currency::GBP());
        $amounts = [];
    
        foreach ($ratios as &$ratio) {
            $ratio = Ratio::fromString($ratio);
        }
        
        foreach ($a->allocate(...$ratios) as $money) {
            $amounts[] = $money->amount();
        }
        
        $this->assertSame($expected, $amounts);
    }
    
    
    public function allocateByRatiosProvider()
    {
        return [
            [10000, [], []],
            [10000, ['1:2', '1:2'], [5000, 5000]],
            [10000, ['1:1', '1:1'], [5000, 5000]],
            [10000, ['1:3', '1:3', '1:3'], [3334, 3333, 3333]],
            [10000, ['1:4', '1:4', '1:2'], [2500, 2500, 5000]],
        ];
    }
}
