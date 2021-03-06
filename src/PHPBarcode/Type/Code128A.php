<?php
namespace PHPBarcode\Type;

/**
 * PHPBarcode - Barcode class helper
 *
 * @author    Davide Marchetti
 * @copyright 2013 David Tufts, 2015 Davide Marchetti
 *
 * @package   PHPBarcode\Type
 * @version   1.0
 * 
 * @link      https://github.com/dvdmarchetti/php-barcode-generator
 *            (original project) https://github.com/davidscotttufts/php-barcode
 *
 * @license
 * The MIT License (MIT)
 * 
 * Copyright (c) 2013 David Tufts, 2015 Davide Marchetti
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 * the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

use PHPBarcode\Type\IBarcodeType as IBarcodeType;
use PHPBarcode\Exception\InvalidBarcodeContentException as InvalidBarcodeContentException;

class Code128A implements IBarcodeType
{
    /**
     * Conversion lookup table for Code128 and Code128b
     * @var array
     */
    private static $_lookUpTable = array(' ' => '212222', '!' => '222122', '"' => '222221', '#' => '121223', '$' => '121322', '%' => '131222', '&' => '122213', '\'' => '122312', '(' => '132212', ')' => '221213', '*' => '221312', '+' => '231212', ',' => '112232', '-' => '122132', '.' => '122231', '/' => '113222', '0' => '123122', '1' => '123221', '2' => '223211', '3' => '221132', '4' => '221231', '5' => '213212', '6' => '223112', '7' => '312131', '8' => '311222', '9' => '321122', ':' => '321221', ';' => '312212', '<' => '322112', '=' => '322211', '>' => '212123', '?' => '212321', '@' => '232121', 'A' => '111323', 'B' => '131123', 'C' => '131321', 'D' => '112313', 'E' => '132113', 'F' => '132311', 'G' => '211313', 'H' => '231113', 'I' => '231311', 'J' => '112133', 'K' => '112331', 'L' => '132131', 'M' => '113123', 'N' => '113321', 'O' => '133121', 'P' => '313121', 'Q' => '211331', 'R' => '231131', 'S' => '213113', 'T' => '213311', 'U' => '213131', 'V' => '311123', 'W' => '311321', 'X' => '331121', 'Y' => '312113', 'Z' => '312311', '[' => '332111', '\\' => '314111', ']' => '221411', '^' => '431111', '_' => '111224', 'NUL' => '111422', 'SOH' => '121124', 'STX' => '121421', 'ETX' => '141122', 'EOT' => '141221', 'ENQ' => '112214', 'ACK' => '112412', 'BEL' => '122114', 'BS' => '122411', 'HT' => '142112', 'LF' => '142211', 'VT' => '241211', 'FF' => '221114', 'CR' => '413111', 'SO' => '241112', 'SI' => '134111', 'DLE' => '111242', 'DC1' => '121142', 'DC2' => '121241', 'DC3' => '114212', 'DC4' => '124112', 'NAK' => '124211', 'SYN' => '411212', 'ETB' => '421112', 'CAN' => '421211', 'EM' => '212141', 'SUB' => '214121', 'ESC' => '412121', 'FS' => '111143', 'GS' => '111341', 'RS' => '131141', 'US' => '114113', 'FNC 3' => '114311', 'FNC 2' => '411113', 'SHIFT' => '411311', 'CODE C' => '113141', 'CODE B' => '114131', 'FNC 4' => '311141', 'FNC 1' => '411131', 'Start A' => '211412', 'Start B' => '211214', 'Start C' => '211232', 'Stop' => '2331112');

    /**
     * Values used for checksum
     * @var array
     */
    private static $_lookUpKeys;
    private static $_lookUpValues;

    /**
     * Regex to validate allowed chars
     * @var string
     */
    private static $_allowedChars = '/^[\\\A-Z\d\s\!\"\#\$\%\&\'\(\)\*\+\,\-\.\/\:\;\<\=\>\?\@\[\]\^\_]+$/';

    /**
     * Checksum base value
     * @var integer
     */
    private static $_checksumBase = 103;

    /**
     * Code prefix
     * @var string
     */
    private static $_prefix = '211412';

    /**
     * Code suffix
     * @var string
     */
    private static $_suffix = '2331112';

    /**
     * Build LookUpKeys and LookUpValues arrays
     */
    public function __construct()
    {
        self::$_lookUpKeys   = array_keys(self::$_lookUpTable);
        self::$_lookUpValues = array_flip(self::$_lookUpKeys);
    }

    /**
     * Transform text to barcode
     * @param  string $input Text which will be converted to barcode format
     * @return string        Converted text in current barcode format data
     */
    public function convert($input)
    {
        // Exception if text is empty
        if (empty($input)) {
            throw new InvalidBarcodeContentException();
        }

        // Convert text to lowercase (no lowercase allowed)
        $input = strtoupper($input);

        // Convert text based on LookUpTable
        $output = '';
        $checksum = self::$_checksumBase;
        for ($x = 1; $x <= strlen($input); $x++) {
            $charIndex = substr($input, ($x - 1), 1);
            $output .= self::$_lookUpTable[$charIndex];

            $checksum += self::$_lookUpValues[$charIndex] * $x;
        }
        // Append checksum value
        $output .= self::$_lookUpTable[self::$_lookUpKeys[($checksum - (intval($checksum / 103) * 103))]];

        // Return barcode data
        return self::$_prefix . $output . self::$_suffix;
    }

    public function validate($input)
    {
        return preg_match(self::$_allowedChars, strtoupper($input));
    }
}
