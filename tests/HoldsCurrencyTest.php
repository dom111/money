<?php

namespace Krixon\Money\Test;

use Krixon\Money\Currency;
use Krixon\Money\CurrencyHolder;
use Krixon\Money\HoldsCurrency;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Krixon\Money\HoldsCurrency
 * @covers ::<protected>
 * @covers ::<private>
 */
class HoldsCurrencyTest extends TestCase
{
    /**
     * @covers ::currency
     */
    public function testCanAccessHeldCurrency() : void
    {
        $currency = Currency::GBP();
        $holder   = $this->implementation($currency);

        static::assertTrue($currency->equals($holder->currency()));
    }


    /**
     * @covers ::usesCurrency
     */
    public function testCanDetermineIfUsesCurrency() : void
    {
        $currency = Currency::GBP();
        $holder   = $this->implementation($currency);

        static::assertTrue($holder->usesCurrency($currency));
    }


    /**
     * @covers ::usesSameCurrencyAs
     */
    public function testCanDetermineIfUsesSameCurrencyAsOtherHolder() : void
    {
        $gbp     = Currency::GBP();
        $usd     = Currency::USD();
        $holder1 = $this->implementation($gbp);
        $holder2 = $this->implementation($gbp);
        $holder3 = $this->implementation($usd);

        static::assertTrue($holder1->usesSameCurrencyAs($holder2));
        static::assertFalse($holder1->usesSameCurrencyAs($holder3));
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
