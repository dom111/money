<?php

namespace Krixon\Money\Exception;

use Krixon\Money\Currency;

class IllegalCurrencyException extends \DomainException
{
    const INCOMPATIBLE_MONEY_CURRENCIES = 1;
    
    
    /**
     * @param Currency[] $currencies
     *
     * @return IllegalCurrencyException
     */
    public static function incompatibleMoneyCurrencies(Currency ...$currencies) : IllegalCurrencyException
    {
        if (empty($currencies)) {
            $message = 'Moneys have different currencies.';
        } else {
            $currencyStrings = array_map(
                function (Currency $currency) {
                    return $currency->toString();
                },
                $currencies
            );
            
            $message = sprintf('Moneys have different currencies: %s.', implode(', ', $currencyStrings));
        }
        
        return new static($message, self::INCOMPATIBLE_MONEY_CURRENCIES);
    }
}
