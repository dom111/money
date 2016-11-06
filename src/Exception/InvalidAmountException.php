<?php

namespace Krixon\Money\Exception;

/**
 * Thrown when an invalid amount is specified for a currency.
 *
 * For example, JPY has a minor unit exponent of 0, so an amount of ¥100.50 is not valid.
 */
class InvalidAmountException extends \DomainException
{
    
}
