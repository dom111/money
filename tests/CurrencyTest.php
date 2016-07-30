<?php

namespace Krixon\Money\Test;

use Krixon\Money\Currency;

class CurrencyTest extends \PHPUnit_Framework_TestCase
{
    public function testCanInstantiateWithMagicCurrencyCodeMethod()
    {
        $currency = Currency::USD();
        
        $this->assertInstanceOf(Currency::class, $currency);
    }
}
