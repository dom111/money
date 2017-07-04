<?php

namespace Krixon\Money;

trait HoldsCurrency
{
    /**
     * @var Currency
     */
    protected $currency;


    public function currency() : Currency
    {
        return $this->currency;
    }


    public function usesCurrency(Currency $currency) : bool
    {
        return $this->currency->equals($currency);
    }


    public function usesSameCurrencyAs(CurrencyHolder $currencyHolder) : bool
    {
        return $this->usesCurrency($currencyHolder->currency());
    }
}
