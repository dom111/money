<?php

namespace Krixon\Money;

interface CurrencyHolder
{
    public function currency() : Currency;
    public function usesCurrency(Currency $currency) : bool;
    public function usesSameCurrencyAs(CurrencyHolder $currencyHolder) : bool;
}
