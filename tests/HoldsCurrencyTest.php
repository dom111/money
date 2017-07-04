<?php

namespace Krixon\Money\Test;

use Krixon\Money\Currency;
use Krixon\Money\CurrencyHolder;
use Krixon\Money\HoldsCurrency;

/**
 * @coversDefaultClass Krixon\Money\HoldsCurrency
 * @covers ::<protected>
 * @covers ::<private>
 */
class HoldsCurrencyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::currency
     */
    public function testCanAccessHeldCurrency()
    {
        $currency = Currency::GBP();
        $holder   = $this->implementation($currency);

        self::assertInstanceOf(Currency::class, $holder->currency());
        self::assertTrue($currency->equals($holder->currency()));
    }


    /**
     * @covers ::usesCurrency
     */
    public function testCanDetermineIfUsesCurrency()
    {
        $currency = Currency::GBP();
        $holder   = $this->implementation($currency);

        self::assertTrue($holder->usesCurrency($currency));
    }


    /**
     * @covers ::usesSameCurrencyAs
     */
    public function testCanDetermineIfUsesSameCurrencyAsOtherHolder()
    {
        $gbp     = Currency::GBP();
        $usd     = Currency::USD();
        $holder1 = $this->implementation($gbp);
        $holder2 = $this->implementation($gbp);
        $holder3 = $this->implementation($usd);

        self::assertTrue($holder1->usesSameCurrencyAs($holder2));
        self::assertFalse($holder1->usesSameCurrencyAs($holder3));
    }


    private function implementation(Currency $currency) : CurrencyHolder
    {
        return new class($currency) implements CurrencyHolder
        {
            use HoldsCurrency;

            public function __construct(Currency $currency)
            {
                $this->currency = $currency;
            }
        };
    }
}
