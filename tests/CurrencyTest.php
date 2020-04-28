<?php

namespace Krixon\Money\Test;

use Krixon\Money\Exception\UnknownCurrencyException;
use Krixon\Money\Currency;
use PHPUnit\Framework\TestCase;

class CurrencyTest extends TestCase
{
    public function testCanInstantiateWithMagicCurrencyCodeMethod() : void
    {
        $currency = Currency::USD();
        
        static::assertInstanceOf(Currency::class, $currency);
    }

    public function testThrowsOnUnknownCurrencyCode() : void
    {
        $this->expectException(UnknownCurrencyException::class);

        new Currency('UNKNOWN');
    }
}
