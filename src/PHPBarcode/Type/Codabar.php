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

class Codabar implements IBarcodeType
{
    /**
     * Conversion lookup table for Code128 and Code128b
     * @var array
     */
    private static $_lookUpTable = array('1' => '1111221', '2' => '1112112', '3' => '2211111', '4' => '1121121', '5' => '2111121', '6' => '1211112', '7' => '1211211', '8' => '1221111', '9' => '2112111', '0' => '1111122', '-' => '1112211', '$' => '1122111', ':' => '2111212', '/' => '2121112', '.' => '2121211', '+' => '1121212', 'A' => '1122121', 'B' => '1212112', 'C' => '1112122', 'D' => '1112221');

    /**
     * Regex to validate allowed chars
     * @var string
     */
    private static $_allowedChars = '/^[\dABCD\-\$\:\/\.\+]+$/';

    /**
     * Code prefix
     * @var string
     */
    private static $_prefix = '11221211';

    /**
     * Code suffix
     * @var string
     */
    private static $_suffix = '1122121';

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
