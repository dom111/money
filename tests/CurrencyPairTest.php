<?php

namespace Krixon\Money\Test;

use Krixon\Money\CurrencyPair;
use Krixon\Money\Money;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Krixon\Money\CurrencyPair
 * @covers ::<protected>
 * @covers ::<private>
 */
class CurrencyPairTest extends TestCase
{
    /**
     * @dataProvider isoStringInstantiationProvider
     * @covers ::fromIsoString
     *
     * @param string $iso
     * @param string $base
     * @param string $counter
     * @param string $ratio
     */
    public function testCanInstantiateFromIsoString(string $iso, string $base, string $counter, string $ratio)
    {
        $pair = CurrencyPair::fromIsoString($iso);
        
        static::assertInstanceOf(CurrencyPair::class, $pair);
        static::assertSame($base, $pair->baseCurrency()->code());
        static::assertSame($counter, $pair->counterCurrency()->code());
        static::assertSame($ratio, $pair->ratio()->toString());
    }
    
    
    public function isoStringInstantiationProvider() : array
    {
        return [
            ['EUR/USD 1.2500', 'EUR', 'USD', '5:4'],
            ['EUR/USD 1', 'EUR', 'USD', '1:1'],
            ['GBP/USD 0.75', 'GBP', 'USD', '3:4'],
        ];
    }
    
    
    /**
     * @dataProvider moneyConversionProvider
     * @covers ::convert
     *
     * @param int    $amount
     * @param string $iso
     * @param int    $expected
     */
    public function testCanConvertMoneyUsingBaseCurrency(int $amount, string $iso, int $expected)
    {
        $pair   = CurrencyPair::fromIsoString($iso);
        $money  = new Money($amount, $pair->baseCurrency());
        $result = $pair->convert($money);
        
        static::assertSame($expected, $result->amount());
        static::assertTrue($result->isInCurrency($pair->counterCurrency())); // Converted from base to counter.
    }
    
    
    public function moneyConversionProvider() : array
    {
        return [
            [7500, 'GBP/USD 0.7500', 10000], // £75 -> $100.
            [10000, 'USD/GBP 1.3333', 7500], // $100 -> £75.
        ];
    }
    
    
    /**
     * @dataProvider stringConversionProvider
     * @covers ::toString
     *
     * @param string $pair
     * @param string $expected
     */
    public function testCanConvertToString(string $pair, string $expected)
    {
        $pair = CurrencyPair::fromIsoString($pair);
        
        static::assertSame($expected, $pair->toString());
    }
    
    
    public function stringConversionProvider() : array
    {
        return [
            ['GBP/USD 1', 'British Pound Sterling (GBP)/United States Dollar (USD) @ 1.0000 (1:1)'],
            ['GBP/USD 1.0000', 'British Pound Sterling (GBP)/United States Dollar (USD) @ 1.0000 (1:1)'],
            ['GBP/USD 1.2525', 'British Pound Sterling (GBP)/United States Dollar (USD) @ 1.2525 (501:400)'],
        ];
    }
    
    
    /**
     * @dataProvider isoStringProvider
     * @covers ::toIsoString
     *
     * @param string $pair
     */
    public function testCanConvertToIsoString(string $pair)
    {
        $pairObj = CurrencyPair::fromIsoString($pair);
        
        static::assertSame($pair, $pairObj->toIsoString());
    }
    
    
    public function isoStringProvider() : array
    {
        return [
            ['GBP/USD 1.0000'],
            ['GBP/USD 1.2525'],
        ];
    }
}
