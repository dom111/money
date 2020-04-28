<?php

namespace Krixon\Money;

use Domain\Money\Exception;
use JsonSerializable;

/**
 * Value object for a currency.
 *
 * @method static Currency AED()
 * @method static Currency AFN()
 * @method static Currency ALL()
 * @method static Currency AMD()
 * @method static Currency ANG()
 * @method static Currency AOA()
 * @method static Currency ARS()
 * @method static Currency AUD()
 * @method static Currency AWG()
 * @method static Currency AZN()
 * @method static Currency BAM()
 * @method static Currency BBD()
 * @method static Currency BDT()
 * @method static Currency BGN()
 * @method static Currency BHD()
 * @method static Currency BIF()
 * @method static Currency BMD()
 * @method static Currency BND()
 * @method static Currency BOB()
 * @method static Currency BRL()
 * @method static Currency BSD()
 * @method static Currency BTC()
 * @method static Currency BTN()
 * @method static Currency BWP()
 * @method static Currency BYR()
 * @method static Currency BZD()
 * @method static Currency CAD()
 * @method static Currency CDF()
 * @method static Currency CHF()
 * @method static Currency CLF()
 * @method static Currency CLP()
 * @method static Currency CNY()
 * @method static Currency COP()
 * @method static Currency CRC()
 * @method static Currency CUP()
 * @method static Currency CVE()
 * @method static Currency CZK()
 * @method static Currency DJF()
 * @method static Currency DKK()
 * @method static Currency DOP()
 * @method static Currency DZD()
 * @method static Currency EEK()
 * @method static Currency EGP()
 * @method static Currency ETB()
 * @method static Currency EUR()
 * @method static Currency FJD()
 * @method static Currency FKP()
 * @method static Currency GBP()
 * @method static Currency GEL()
 * @method static Currency GHS()
 * @method static Currency GIP()
 * @method static Currency GMD()
 * @method static Currency GNF()
 * @method static Currency GTQ()
 * @method static Currency GYD()
 * @method static Currency HKD()
 * @method static Currency HNL()
 * @method static Currency HRK()
 * @method static Currency HTG()
 * @method static Currency HUF()
 * @method static Currency IDR()
 * @method static Currency ILS()
 * @method static Currency INR()
 * @method static Currency IQD()
 * @method static Currency IRR()
 * @method static Currency ISK()
 * @method static Currency JEP()
 * @method static Currency JMD()
 * @method static Currency JOD()
 * @method static Currency JPY()
 * @method static Currency KES()
 * @method static Currency KGS()
 * @method static Currency KHR()
 * @method static Currency KMF()
 * @method static Currency KPW()
 * @method static Currency KRW()
 * @method static Currency KWD()
 * @method static Currency KYD()
 * @method static Currency KZT()
 * @method static Currency LAK()
 * @method static Currency LBP()
 * @method static Currency LKR()
 * @method static Currency LRD()
 * @method static Currency LSL()
 * @method static Currency LTL()
 * @method static Currency LVL()
 * @method static Currency LYD()
 * @method static Currency MAD()
 * @method static Currency MDL()
 * @method static Currency MGA()
 * @method static Currency MKD()
 * @method static Currency MMK()
 * @method static Currency MNT()
 * @method static Currency MOP()
 * @method static Currency MRO()
 * @method static Currency MUR()
 * @method static Currency MVR()
 * @method static Currency MWK()
 * @method static Currency MXN()
 * @method static Currency MYR()
 * @method static Currency MZN()
 * @method static Currency NAD()
 * @method static Currency NGN()
 * @method static Currency NIO()
 * @method static Currency NOK()
 * @method static Currency NPR()
 * @method static Currency NZD()
 * @method static Currency OMR()
 * @method static Currency PAB()
 * @method static Currency PEN()
 * @method static Currency PGK()
 * @method static Currency PHP()
 * @method static Currency PKR()
 * @method static Currency PLN()
 * @method static Currency PYG()
 * @method static Currency QAR()
 * @method static Currency RON()
 * @method static Currency RSD()
 * @method static Currency RUB()
 * @method static Currency RWF()
 * @method static Currency SAR()
 * @method static Currency SBD()
 * @method static Currency SCR()
 * @method static Currency SDG()
 * @method static Currency SEK()
 * @method static Currency SGD()
 * @method static Currency SHP()
 * @method static Currency SLL()
 * @method static Currency SOS()
 * @method static Currency SRD()
 * @method static Currency STD()
 * @method static Currency SVC()
 * @method static Currency SYP()
 * @method static Currency SZL()
 * @method static Currency THB()
 * @method static Currency TJS()
 * @method static Currency TMT()
 * @method static Currency TND()
 * @method static Currency TOP()
 * @method static Currency TRY()
 * @method static Currency TTD()
 * @method static Currency TWD()
 * @method static Currency TZS()
 * @method static Currency UAH()
 * @method static Currency UGX()
 * @method static Currency USD()
 * @method static Currency UYU()
 * @method static Currency UZS()
 * @method static Currency VEF()
 * @method static Currency VND()
 * @method static Currency VUV()
 * @method static Currency WST()
 * @method static Currency XAF()
 * @method static Currency XAG()
 * @method static Currency XAU()
 * @method static Currency XCD()
 * @method static Currency XDR()
 * @method static Currency XOF()
 * @method static Currency XPD()
 * @method static Currency XPF()
 * @method static Currency XTS()
 * @method static Currency XXX()
 * @method static Currency YER()
 * @method static Currency ZAR()
 * @method static Currency ZMK()
 * @method static Currency ZWL()
 */
class Currency implements JsonSerializable
{
    /**
     * @var string
     */
    private $code;
    
    /**
     * @var string[]
     */
    static private $codes = [
        'AED' => 'United Arab Emirates Dirham',
        'AFN' => 'Afghan Afghani',
        'ALL' => 'Albanian Lek',
        'AMD' => 'Armenian Dram',
        'ANG' => 'Netherlands Antillean Guilder',
        'AOA' => 'Angolan Kwanza',
        'ARS' => 'Argentine Peso',
        'AUD' => 'Australian Dollar',
        'AWG' => 'Aruban Florin',
        'AZN' => 'Azerbaijani Manat',
        'BAM' => 'Bosnia-Herzegovina Convertible Mark',
        'BBD' => 'Barbadian Dollar',
        'BDT' => 'Bangladeshi Taka',
        'BGN' => 'Bulgarian Lev',
        'BHD' => 'Bahraini Dinar',
        'BIF' => 'Burundian Franc',
        'BMD' => 'Bermudan Dollar',
        'BND' => 'Brunei Dollar',
        'BOB' => 'Bolivian Boliviano',
        'BRL' => 'Brazilian Real',
        'BSD' => 'Bahamian Dollar',
        'BTC' => 'Bitcoin',
        'BTN' => 'Bhutanese Ngultrum',
        'BWP' => 'Botswanan Pula',
        'BYR' => 'Belarusian Ruble',
        'BZD' => 'Belize Dollar',
        'CAD' => 'Canadian Dollar',
        'CDF' => 'Congolese Franc',
        'CHF' => 'Swiss Franc',
        'CLF' => 'Chilean Unit of Account (UF)',
        'CLP' => 'Chilean Peso',
        'CNY' => 'Chinese Yuan',
        'COP' => 'Colombian Peso',
        'CRC' => 'Costa Rican Colón',
        'CUP' => 'Cuban Peso',
        'CVE' => 'Cape Verdean Escudo',
        'CZK' => 'Czech Republic Koruna',
        'DJF' => 'Djiboutian Franc',
        'DKK' => 'Danish Krone',
        'DOP' => 'Dominican Peso',
        'DZD' => 'Algerian Dinar',
        'EEK' => 'Estonian Kroon',
        'EGP' => 'Egyptian Pound',
        'ETB' => 'Ethiopian Birr',
        'EUR' => 'Euro',
        'FJD' => 'Fijian Dollar',
        'FKP' => 'Falkland Islands Pound',
        'GBP' => 'British Pound Sterling',
        'GEL' => 'Georgian Lari',
        'GHS' => 'Ghanaian Cedi',
        'GIP' => 'Gibraltar Pound',
        'GMD' => 'Gambian Dalasi',
        'GNF' => 'Guinean Franc',
        'GTQ' => 'Guatemalan Quetzal',
        'GYD' => 'Guyanaese Dollar',
        'HKD' => 'Hong Kong Dollar',
        'HNL' => 'Honduran Lempira',
        'HRK' => 'Croatian Kuna',
        'HTG' => 'Haitian Gourde',
        'HUF' => 'Hungarian Forint',
        'IDR' => 'Indonesian Rupiah',
        'ILS' => 'Israeli New Sheqel',
        'INR' => 'Indian Rupee',
        'IQD' => 'Iraqi Dinar',
        'IRR' => 'Iranian Rial',
        'ISK' => 'Icelandic Króna',
        'JEP' => 'Jersey Pound',
        'JMD' => 'Jamaican Dollar',
        'JOD' => 'Jordanian Dinar',
        'JPY' => 'Japanese Yen',
        'KES' => 'Kenyan Shilling',
        'KGS' => 'Kyrgystani Som',
        'KHR' => 'Cambodian Riel',
        'KMF' => 'Comorian Franc',
        'KPW' => 'North Korean Won',
        'KRW' => 'South Korean Won',
        'KWD' => 'Kuwaiti Dinar',
        'KYD' => 'Cayman Islands Dollar',
        'KZT' => 'Kazakhstani Tenge',
        'LAK' => 'Laotian Kip',
        'LBP' => 'Lebanese Pound',
        'LKR' => 'Sri Lankan Rupee',
        'LRD' => 'Liberian Dollar',
        'LSL' => 'Lesotho Loti',
        'LTL' => 'Lithuanian Litas',
        'LVL' => 'Latvian Lats',
        'LYD' => 'Libyan Dinar',
        'MAD' => 'Moroccan Dirham',
        'MDL' => 'Moldovan Leu',
        'MGA' => 'Malagasy Ariary',
        'MKD' => 'Macedonian Denar',
        'MMK' => 'Myanma Kyat',
        'MNT' => 'Mongolian Tugrik',
        'MOP' => 'Macanese Pataca',
        'MRO' => 'Mauritanian Ouguiya',
        'MUR' => 'Mauritian Rupee',
        'MVR' => 'Maldivian Rufiyaa',
        'MWK' => 'Malawian Kwacha',
        'MXN' => 'Mexican Peso',
        'MYR' => 'Malaysian Ringgit',
        'MZN' => 'Mozambican Metical',
        'NAD' => 'Namibian Dollar',
        'NGN' => 'Nigerian Naira',
        'NIO' => 'Nicaraguan Córdoba',
        'NOK' => 'Norwegian Krone',
        'NPR' => 'Nepalese Rupee',
        'NZD' => 'New Zealand Dollar',
        'OMR' => 'Omani Rial',
        'PAB' => 'Panamanian Balboa',
        'PEN' => 'Peruvian Nuevo Sol',
        'PGK' => 'Papua New Guinean Kina',
        'PHP' => 'Philippine Peso',
        'PKR' => 'Pakistani Rupee',
        'PLN' => 'Polish Zloty',
        'PYG' => 'Paraguayan Guarani',
        'QAR' => 'Qatari Rial',
        'RON' => 'Romanian Leu',
        'RSD' => 'Serbian Dinar',
        'RUB' => 'Russian Ruble',
        'RWF' => 'Rwandan Franc',
        'SAR' => 'Saudi Riyal',
        'SBD' => 'Solomon Islands Dollar',
        'SCR' => 'Seychellois Rupee',
        'SDG' => 'Sudanese Pound',
        'SEK' => 'Swedish Krona',
        'SGD' => 'Singapore Dollar',
        'SHP' => 'Saint Helena Pound',
        'SLL' => 'Sierra Leonean Leone',
        'SOS' => 'Somali Shilling',
        'SRD' => 'Surinamese Dollar',
        'STD' => 'São Tomé and Príncipe Dobra',
        'SVC' => 'Salvadoran Colón',
        'SYP' => 'Syrian Pound',
        'SZL' => 'Swazi Lilangeni',
        'THB' => 'Thai Baht',
        'TJS' => 'Tajikistani Somoni',
        'TMT' => 'Turkmenistani Manat',
        'TND' => 'Tunisian Dinar',
        'TOP' => 'Tongan Paʻanga',
        'TRY' => 'Turkish Lira',
        'TTD' => 'Trinidad and Tobago Dollar',
        'TWD' => 'New Taiwan Dollar',
        'TZS' => 'Tanzanian Shilling',
        'UAH' => 'Ukrainian Hryvnia',
        'UGX' => 'Ugandan Shilling',
        'USD' => 'United States Dollar',
        'UYU' => 'Uruguayan Peso',
        'UZS' => 'Uzbekistan Som',
        'VEF' => 'Venezuelan Bolívar',
        'VND' => 'Vietnamese Dong',
        'VUV' => 'Vanuatu Vatu',
        'WST' => 'Samoan Tala',
        'XAF' => 'CFA Franc BEAC',
        'XAG' => 'Silver',
        'XAU' => 'Gold',
        'XCD' => 'East Caribbean Dollar',
        'XDR' => 'Special Drawing Rights',
        'XOF' => 'CFA Franc BCEAO',
        'XPD' => 'Palladium',
        'XPF' => 'CFP Franc',
        'XTS' => 'Reserved for Testing',
        'XXX' => 'No Currency',
        'YER' => 'Yemeni Rial',
        'ZAR' => 'South African Rand',
        'ZMK' => 'Zambian Kwacha',
        'ZWL' => 'Zimbabwean Dollar',
    ];
    
    /**
     * Exponents for expressing the relationship between major and minor currency units as defined by ISO4217.
     *
     * For convenience only the currencies which do not have an exponent of 2 are included here.
     *
     * @link http://en.wikipedia.org/wiki/ISO_4217#Treatment_of_minor_currency_units_.28the_.22exponent.22.29
     *
     * @var int[]
     */
    static private $exponents = [
        'BHD' => 3,
        'BIF' => 0,
        'BYR' => 0,
        'CLF' => 0,
        'CLP' => 0,
        'CVE' => 0,
        'DJF' => 0,
        'EGP' => 3,
        'GNF' => 0,
        'IQD' => 3,
        'ISK' => 0,
        'JOD' => 3,
        'JPY' => 0,
        'KMF' => 0,
        'KRW' => 0,
        'KWD' => 3,
        'LYD' => 3,
        'OMR' => 3,
        'PYG' => 0,
        'RWF' => 0,
        'TND' => 3,
        'UGX' => 0,
        'VND' => 0,
        'VUV' => 0,
        'XAF' => 0,
        'XAG' => 0,
        'XAU' => 0,
        'XDR' => 0,
        'XOF' => 0,
        'XPD' => 0,
        'XPF' => 0,
        'XXX' => 0,
    ];
    
    
    /**
     * @param string $code The 3-digit ISO4217 currency code.
     */
    public function __construct($code)
    {
        if (!array_key_exists($code, static::$codes)) {
            throw new Exception\UnknownCurrencyException("Unknown ISO4217 currency code '$code'.");
        }
        
        $this->code = $code;
    }
    
    
    public static function __callStatic(string $name, array $arguments)
    {
        return new static(strtoupper($name));
    }
    
    
    /**
     * Returns an array of all supported currency codes.
     *
     * @return string[]
     */
    public static function codes()
    {
        return array_keys(static::$codes);
    }
    
    
    /**
     * Creates a new instance from an ISO currency code.
     *
     * @param string $code
     *
     * @return static
     */
    public static function fromIsoCode($code)
    {
        return new static($code);
    }


    public function jsonSerialize() : array
    {
        return [
            'code'       => $this->code,
            'minorUnits' => $this->minorUnitMultiplier(),
        ];
    }


    /**
     * Returns the ISO currency code.
     *
     * @return string
     */
    public function code()
    {
        return $this->code;
    }
    
    
    /**
     * Returns the name of the currency.
     *
     * @return string
     */
    public function getName()
    {
        return static::$codes[$this->code];
    }
    
    
    /**
     * Returns the exponent which expresses the relationship between the major and minor currency units.
     *
     * @return int
     */
    public function minorUnitExponent()
    {
        return array_key_exists($this->code, static::$exponents) ? static::$exponents[$this->code] : 2;
    }
    
    
    /**
     * @return number
     */
    public function minorUnitMultiplier()
    {
        return pow(10, $this->minorUnitExponent());
    }
    
    
    public function equals(Currency $other)
    {
        return $this->code === $other->code;
    }
    
    
    public function toString()
    {
        return $this->getName() . ' (' . $this->code . ')';
    }
    
    
    public function __toString()
    {
        return $this->toString();
    }
}
