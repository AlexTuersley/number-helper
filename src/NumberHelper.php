<?php

namespace Amt\NumberHelper;

class NumberHelper
{
    
    /**
     * Checks if value is numeric, if it contains commas, removes them and checks again.
     *
     * @param mixed $value
     * @return mixed
     */
    public static function checkValue(mixed $value = null): mixed
    {
        if(!is_numeric($value)){
            return false;
        } elseif(str_contains((string)$value, ',')){
            $value = str_replace(',', '', (string)$value);
            if(!is_numeric($value)){
                return false;
            }
        }
        return $value;
    }

    /**
     * Formats number
     *
     * @param mixed $value
     * @param int $decimals
     * @param string $thousandSeparator
     * @return string
     */
    public static function formatNumber(mixed $value, int $decimals = 0, string $thousandSeparator = ","): string 
    {
        $formattedValue = self::checkValue($value);
        if($formattedValue === false){
            return (string) $value;
        }
        return number_format((float)$formattedValue, $decimals, ".", $thousandSeparator);
    }

    /**
     * Formats number
     *
     * @param float $value
     * @param int $decimals
     * @param string $thousandSeparator
     * @param string $currencyCode
     * @return mixed
     */
    public static function formatPrice(mixed $value = null, int $decimals = 2, string $thousandSeparator = "", string $currencyCode = '') : mixed
    {
        $formattedValue = self::checkValue($value);
        if($formattedValue === false){
            return (string) $value;
        }
        $value = (float)$formattedValue;
        return ($currencyCode ? self::getCurrencySymbolFromCode($currencyCode) : '').number_format($value, $decimals, ".", $thousandSeparator);
    }

    /**
     * Formats bytes to human readable format
     *
     * @param int $bytes
     * @param int $decimals
     * @return string
     */
    public static function formatBytes(int $bytes, int $decimals = 2): string
    {
        $size = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $factor = floor((strlen((string)$bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }

    /**
     * Formats duration from seconds to human readable format
     *
     * @param int $seconds
     * @return string
     */
    public static function formatDuration(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds / 60) % 60);
        $seconds = $seconds % 60;
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    /**
     * Formats duration in a short format, e.g. 1h 20m 30s
     *
     * @param int $seconds
     * @return string
     */
    public static function formatDurationShort(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds / 60) % 60);
        $seconds = $seconds % 60;
        $result = '';
        if($hours > 0){
            $result .= $hours.'h ';
        }
        if($minutes > 0){
            $result .= $minutes.'m ';
        }
        if($seconds > 0 || !$result){
            $result .= $seconds.'s';
        }
        return trim($result);
    }

    /**
     * Adds VAT to price
     *
     * @param float $price
     * @param float $vatPercent
     * @return float
     */
    public static function addVatToPrice(float $price, float $vatPercent): float
    {
        return round($price * (1 + $vatPercent / 100), 2);
    }

    /**
     * Removes VAT from price
     * 
     * @param float $price
     * @param float $vatPercent
     * @return float
     */
    public static function removeVatFromPrice(float $price, float $vatPercent): float
    {
        return round($price / (1 + $vatPercent / 100), 2);
    }

    /**
     * Given 2 values and decimal places, calculates percentage between them two values and formats it to decimal.
     *
     * @param int $value1
     * @param int $value2
     * @param int $decimals
     * @return string
     */
    public static function calculatePercentage(int $mainVal, int $divideVal, int $decimals = 0, string $thousandSeparator = ''): string
    {
        if($mainVal > 0 && $divideVal > 0)
        {
            return self::formatPrice((($mainVal*100)/$divideVal), $decimals, $thousandSeparator)."%";
        }
        else
        {
            return '0%';
        }
    }

    
    /**
     * Convert prices in micro to float values.
     * Usefully for Google Ads cost_micros, etc.
     *
     * @param int $value
     * @return float
     */
    public static function microToPrice(int $value): float
    {
        return (float) $value/1000000;
    }

    /**
     * Convert price to micro value, mainly for Google Ads
     *
     * @param float $value
     * @return int
     */
    public static function priceToMicros(float $value): int
    {
        return (int) (round((float) $value * 1000000));
    }

    /**
     * Convert prices in micro into a formatted decimal value.
     * Makes use of microToPrice and formatPrice functions.
     *
     * @param int $value
     * @param int $decimals
     * @param string $thousandSeparator
     * @return float
     */
    public static function microPricesToDecimal(int $value, int $decimals = 2, string $thousandSeparator = "") : float
    {
        return (float) self::formatPrice(self::microToPrice($value), $decimals, $thousandSeparator);
    }

    /**
     * Get currency symbol from currency code.
     *
     * @param string $currencyCode
     * @return string
     */
    public static function getCurrencySymbolFromCode($currencyCode)
    {
        $currencySymbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            'AUD' => 'A$',
            'CAD' => 'C$',
            'CHF' => 'CHF',
            'CNY' => '¥',
            'SEK' => 'kr',
            'NZD' => 'NZ$',
            'MXN' => '$',
            'SGD' => 'S$',
            'HKD' => 'HK$',
            'NOK' => 'kr',
            'KRW' => '₩',
            'TRY' => '₺',
            'RUB' => '₽',
            'INR' => '₹',
            'BRL' => 'R$',
            'ZAR' => 'R',
            'PHP' => '₱',
            'PLN' => 'zł',
            'IDR' => 'Rp',
            'THB' => '฿',
            'VND' => '₫',
            'MYR' => 'RM',
            'CZK' => 'Kč',
            'HUF' => 'Ft',
            'ILS' => '₪',
            'DKK' => 'kr',
            'CLP' => '$',
            'COP' => '$',
            'SAR' => '﷼',
            'AED' => 'د.إ',
            'TWD' => 'NT$',
            'ARS' => '$',
            'EGP' => '£',
            'NGN' => '₦',
            'PKR' => '₨',
            'BDT' => '৳',
            'LKR' => '₨',
            'KZT' => '₸',
            'QAR' => '﷼',
            'KWD' => 'د.ك',
            'OMR' => 'ر.ع.',
            'JOD' => 'د.ا',
            'BHD' => 'ب.د',
            'DZD' => 'دج',
            'MAD' => 'د.م.',
            'TND' => 'د.ت',
            'PEN' => 'S/',
            'UAH' => '₴',
            'GHS' => '₵',
            'KES' => 'KSh',
            'TZS' => 'TSh',
            'UGX' => 'USh',
            'XAF' => 'FCFA',
            'XOF' => 'CFA',
            'XPF' => 'CFP',
            'RWF' => 'FRw',
            'BWP' => 'P',
            'ZMW' => 'ZK',
            'MUR' => '₨',
            'MZN' => 'MT',
            'ALL' => 'L',
            'AMD' => '֏',
            'AZN' => '₼',
            'BYN' => 'Br',
            'GEL' => '₾',
            'KGS' => 'сом',
            'MDL' => 'L',
            'MKD' => 'ден',
            'TJS' => 'ЅМ',
            'UZS' => 'so\'m',
            'AFN' => '؋',
            'IQD' => 'ع.د',
            'LYD' => 'ل.د',
            'SYP' => '£',
            'YER' => '﷼',
            'ARS' =>'AR$',
            'AUD' => '$',
            'BGN' => 'лв',
            'BND' => '$', 
            'BOB' => 'Bs',
            'BRL' => 'R$',
            'CAD' => '$',
            'CHF' => 'Fr',
            'CLP' => 'CL$',
            'CNY' => '¥',
            'COP' => '$',
            'CSD' => 'CSD',
            'CZK' => 'Kč',
            'DEM' => 'DM', 
            'DKK' => 'kr',
            'EEK' => 'KR',
            'EGP' => '£',
            'EUR' => '€',
            'FJD' => '$',
            'GBP' => '£',
            'HKD' => '$',
            'HRK' => 'kr',
            'HUF' => 'Ft',
            'IDR' => 'Rp',
            'ILS' => '₪',
            'INR' =>'Rs',
            'JOD' => 'د.ا',
            'JPY' => '¥',
            'KES' => 'Sh',
            'KRW' => '₩',
            'LKR' => 'ரூ',
            'LTL' => 'Lt',
            'MAD' => '.د.م',
            'MTL' => 'Lm',
            'MXN' => '$',
            'MYR' => 'RM',
            'NOK' => 'kr',
            'NZD' => '$',
            'PEN' => 'S/.',
            'PHP' => '₱',
            'PKR' => '₨',
            'PLN' => 'zł',
            'ROL' => 'leu',
            'RON' => 'RON',
            'RSD' => 'RSD',
            'RUB' => 'р.',
            'SAR' => 'ر.س',
            'SEK' => 'kr',
            'SGD' => '$',
            'SIT' => 'St',
            'SKK' => 'Sk',
            'THB' => '฿',
            'TND' => 'د.ت',
            'TRL' => '₺',
            'TRY' => '₺',
            'TWD' => '$',
            'UAH' => '₴',
            'USD' => '$',
            'UYU' => '$U',
            'VEB' => 'Bs',
            'VEF' => 'Bs',
            'VND' => '₫',
            'ZAR' => 'R',
            'AED' => 'AED',
            'CRC' => '₡',
            'QAR' => 'QR',
            'KWD' => 'KD'
        ];
        return isset($currencySymbols[$currencyCode]) ? $currencySymbols[$currencyCode] : '';
    }
}