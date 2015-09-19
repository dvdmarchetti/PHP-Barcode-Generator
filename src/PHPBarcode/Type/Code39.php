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

class Code39 implements IBarcodeType
{
    /**
     * Conversion lookup table for Code39
     * @var array
     */
    private static $_lookUpTable = array('0' => '111221211', '1' => '211211112', '2' => '112211112', '3' => '212211111', '4' => '111221112', '5' => '211221111', '6' => '112221111', '7' => '111211212', '8' => '211211211', '9' => '112211211', 'A' => '211112112', 'B' => '112112112', 'C' => '212112111', 'D' => '111122112', 'E' => '211122111', 'F' => '112122111', 'G' => '111112212', 'H' => '211112211', 'I' => '112112211', 'J' => '111122211', 'K' => '211111122', 'L' => '112111122', 'M' => '212111121', 'N' => '111121122', 'O' => '211121121', 'P' => '112121121', 'Q' => '111111222', 'R' => '211111221', 'S' => '112111221', 'T' => '111121221', 'U' => '221111112', 'V' => '122111112', 'W' => '222111111', 'X' => '121121112', 'Y' => '221121111', 'Z' => '122121111', '-' => '121111212', '.' => '221111211', ' ' => '122111211', '$' => '121212111', '/' => '121211121', '+' => '121112121', '%' => '111212121', '*' => '121121211');

    /**
     * Regex to validate allowed chars
     * @var string
     */
    private static $_allowedChars = '/^[\dA-Z\-\.\s\$\/\+\%\*]+$/';

    /**
     * Code prefix
     * @var string
     */
    private static $_prefix = '1211212111';

    /**
     * Code suffix
     * @var string
     */
    private static $_suffix = '1211212111';

    /**
     * Transform text to barcode
     * @param  string $input Text which will be converted to barcode format
     * @return string        Converted text in current barcode format
     */
    public function convert($input)
    {
        // Exception if text is empty
        if (empty($input)) {
            throw new InvalidBarcodeContentException();
        }

        // Convert text to lowercase (no lowercase allowed)
        $input = strtoupper($input);

        // Check against unwanded chars
        if (preg_match(self::$_allowedChars, $input) == false) {
            throw new InvalidBarcodeContentException();
        }

        // Convert text based on LookUpTable
        $output = '';
        for ($x = 1; $x <= strlen($input); $x++) {
            $charIndex = substr($input, ($x - 1), 1);
            $output .= self::$_lookUpTable[$charIndex] . '1';
        }

        // Return barcode data
        return self::$_prefix . $output . self::$_suffix;
    }

    public function validate($input)
    {
        return preg_match(self::$_allowedChars, strtoupper($input));
    }
}
