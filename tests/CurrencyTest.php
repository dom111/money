<?php

namespace Krixon\Money\Test;

use Krixon\Money\Currency;
use PHPUnit\Framework\TestCase;

class CurrencyTest extends TestCase
{
    public function testCanInstantiateWithMagicCurrencyCodeMethod()
    {
        $currency = Currency::USD();
        
        static::assertInstanceOf(Currency::class, $currency);
    }
}
