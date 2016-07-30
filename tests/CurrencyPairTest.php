<?php

namespace Krixon\Money\Test;

use Krixon\Money\CurrencyPair;
use Krixon\Money\Money;

/**
 * @coversDefaultClass Krixon\Money\CurrencyPair
 * @covers ::<protected>
 * @covers ::<private>
 */
class CurrencyPairTest extends \PHPUnit_Framework_TestCase
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
        
        self::assertInstanceOf(CurrencyPair::class, $pair);
        self::assertSame($base, $pair->baseCurrency()->code());
        self::assertSame($counter, $pair->counterCurrency()->code());
        self::assertSame($ratio, $pair->ratio()->toString());
    }
    
    
    public function isoStringInstantiationProvider()
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
    public function testCanConvertMoneyUsingBaseCurrency(
        int $amount,
        string $iso,
        int $expected
    ) {
        $pair   = CurrencyPair::fromIsoString($iso);
        $money  = new Money($amount, $pair->baseCurrency());
        $result = $pair->convert($money);
        
        self::assertSame($expected, $result->amount());
        self::assertTrue($result->isInCurrency($pair->counterCurrency())); // Converted from base to counter.
    }
    
    
    public function moneyConversionProvider()
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
        
        self::assertSame($expected, $pair->toString());
    }
    
    
    public function stringConversionProvider()
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
        
        self::assertSame($pair, $pairObj->toIsoString());
    }
    
    
    public function isoStringProvider()
    {
        return [
            ['GBP/USD 1.0000'],
            ['GBP/USD 1.2525'],
        ];
    }
}
