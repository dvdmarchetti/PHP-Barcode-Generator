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

class Code25 implements IBarcodeType
{
    /**
     * Conversion lookup table for Code25
     * @var array
     */
    private static $_lookUpTable = array('1' => '3-1-1-1-3', '2' => '1-3-1-1-3', '3' => '3-3-1-1-1', '4' => '1-1-3-1-3', '5' => '3-1-3-1-1', '6' => '1-3-3-1-1', '7' => '1-1-1-3-3', '8' => '3-1-1-3-1', '9' => '1-3-1-3-1', '0' => '1-1-3-3-1');

    /**
     * Regex to validate allowed chars
     * @var string
     */
    private static $_allowedChars = '/^[\d]+$/';

    /**
     * Code prefix
     * @var string
     */
    private static $_prefix = "1111";

    /**
     * Code suffix
     * @var string
     */
    private static $_suffix = "311";

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

        // Convert text based on LookUpTable
        $output = '';
        $chars = array();
        for ($i = 1; $i <= strlen($input); $i++) {
            $charIndex = substr($input, ($i - 1), 1);
            $chars[] = self::$_lookUpTable[$charIndex];
        }

        for ($i = 0; $i < strlen($input); $i+= 2) {
            if (isset($chars[$i]) && isset($chars[$i + 1])) {
                $values  = explode('-', $chars[$i]);
                $values2 = explode('-', $chars[$i + 1]);

                for ($j = 0; $j < count($values); $j++) {
                    $output .= $values[$j] . $values2[$j];
                }
            }
        }

        // Return barcode data
        return self::$_prefix . $output . self::$_suffix;
    }

    public function validate($input)
    {
        return preg_match(self::$_allowedChars, strtoupper($input));
    }
}
