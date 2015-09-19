<?php
namespace PHPBarcode;

/**
 * PHPBarcode - Barcode class helper
 *
 * @author    Davide Marchetti
 * @copyright 2013 David Tufts, 2015 Davide Marchetti
 *
 * @package   PHPBarcode
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
use PHPBarcode\Exception\InvalidBarcodeSizeException as InvalidBarcodeSizeException;
use PHPBarcode\Exception\InvalidBarcodeContentException as InvalidBarcodeContentException;
use PHPBarcode\Exception\InvalidBarcodeOrientationException as InvalidBarcodeOrientationException;

class Barcode
{
    /**
     * Barcode orientation type:
     * - Horizontal barcode (default)
     * - Vertical barcode
     */
    const BARCODE_ORIENTATION_HORIZONTAL = 1;
    const BARCODE_ORIENTATION_VERTICAL   = 2;

    /**
     * Barcode left padding (minimum value = 5)
     * @var integer
     */
    private $_pad = 20;

    /**
     * Barcode type (default = BARCODE_TYPE_CODE128)
     * @var IBarcodeType object
     */
    private $_type;

    /**
     * Temporary input text
     * @var string
     */
    private $_inputText;

    /**
     * Temporary output text
     * @var string
     */
    private $_outputText;

    /**
     * Barcode sizes
     * @var array('width' => (int), 'height' => (int))
     */
    private $_dimensions;

    /**
     * Barcode image generated with GD Image Library
     * @var Resource
     */
    private $_barcode;

    /**
     * Barcode orientation (default = BARCODE_ORIENTATION_HORIZONTAL)
     * @var integer
     */
    private $_orientation = self::BARCODE_ORIENTATION_HORIZONTAL;

    /**
     * Initialize new barcode
     * 
     * @param IBarcodeType $type        Type of barcode
     * @param string       $text        Barcode content
     * @param integer      $size        Height (for horizontal) or width (for vertical)  of barcode
     * @param integer      $orientation Orientation of barcode
     */
    public function __construct (IBarcodeType $type, $text, $dimensions = null, $orientation = null)
    {
        $this->_type = $type;

        $this->_dimensions = array(
            'width'  => 35,
            'height' => 35
        );

        if ($text !== null) {
            if (empty($text)) {
                throw new InvalidBarcodeContentException();
            }

            if (!$this->_type->validate($text)) {
                throw new InvalidBarcodeContentException();
            }

            $this->_inputText = $text;
        }

        if ($dimensions !== null) {
            $this->setDimensions($dimensions);
        }

        if ($orientation !== null) {
            if (is_int($orientation) && $orientation != self::BARCODE_ORIENTATION_VERTICAL && $orientation != self::BARCODE_ORIENTATION_HORIZONTAL) {
                throw new InvalidBarcodeOrientationException();
            }

            $this->_orientation = $orientation;
        }

        if ($this->_pad < 10) {
            $this->_pad = 10;
        }
    }

    /**
     * Set barcode type
     * 
     * @param  IBarcodetype $type Object which implements IBarcodeType interface
     * @return null
     */
    public function setType(IBarcodeType $type)
    {
        $this->_type = $type;
    }

    /**
     * Set barcode width
     * 
     * @param integer $width Barcode width in pixels
     */
    public function setWidth($width)
    {
        if (!is_int($width)) {
            throw new \InvalidArgumentException();
        }

        if ($width == 0) {
            throw new InvalidBarcodeSizeException();
        }

        $this->_dimensions['width'] = $width;
    }

    /**
     * Set barcode height
     * 
     * @param integer $height Barcode height in pixels
     */
    public function setHeight($height)
    {
        if (!is_int($height)) {
            throw new \InvalidArgumentException();
        }

        if ($height == 0) {
            throw new InvalidBarcodeSizeException();
        }

        $this->_dimensions['height'] = $height;
    }

    /**
     * Set width and height
     * 
     * @param miexd   $wh     (array) To set width and height at same time - (integer) to set width and height separate
     * @param integer $height (optional) If first param is integer, you must specify an height here
     */
    public function setDimensions($wh, $height = null)
    {
        if (is_array($wh)) {
            if (is_null($wh['width']) || is_null($wh['height'])) {
                throw new \InvalidArgumentException();
            }

            $this->setWidth($wh['width']);
            $this->setHeight($wh['height']);
        } else {
            if (is_null($wh) || is_null($height)) {
                throw new \InvalidArgumentException();
            }

            $this->setWidth($wh);
            $this->setHeight($height);
        }
    }

    /**
     * Set barcode text
     * 
     * @param  string $text Barcode content
     * @return null
     */
    public function setText($text)
    {
        if (empty($text)) {
            throw new InvalidBarcodeContentException();
        }

        // If is not valid text for this type of barcode, throw an Exception
        if (!$this->_type->validate($text)) {
            throw new InvalidBarcodeContentException();
        }

        $this->_inputText = $text;
    }

    /**
     * Set barcode orientation
     * 
     * @param integer $orientation Barcode orientation (use Class constants)
     */
    public function setOrientation($orientation)
    {
        if ($orientation !== null) {
            // If is not valid orientation, throw an exception
            if (is_int($orientation) && $orientation != self::BARCODE_ORIENTATION_VERTICAL && $orientation != self::BARCODE_ORIENTATION_HORIZONTAL) {
                throw new InvalidBarcodeOrientationException();
            }

            $this->_orientation = $orientation;
        }
    }

    /**
     * Create and output barcode inline (changes header of page)
     *
     * @return null
     */
    public function output()
    {
        if ($this->_barcode === null) {
            $this->_make();
        }

        header('Content-type: image/png');
        imagepng($this->_barcode);
        imagedestroy($this->_barcode);
        $this->_barcode = null;
    }

    /**
     * Save barcode on filesystem.
     * If specified file already exists a new name will be generated
     * using PHP's uniqid().
     * 
     * @param  string $filename (optional) Specify name for the file
     * @return string           Name of saved file
     */
    public function save($filename = null)
    {
        if ($this->_barcode === null) {
            $this->_make();
        }


        if ($filename === null || ($filename !== null && file_exists($filename))) {
            $filename = uniqid('', true);
        }

        if (empty($filename)) {
            throw new IllegalArgumentException();
        }

        $filename .= '.png';

        imagepng($this->_barcode, $filename);
        imagedestroy($this->_barcode);
        $this->_barcode = null;

        return $filename;
    }

    /**
     * Output data64 content of image (can be used in <img src="$barcode->outputAsDataUrl(); ?>"> tags)
     * 
     * @return string data64 content of image
     */
    public function outputAsDataUrl()
    {
        $filename = $this->save();

        if (!file_exists($filename)) {
            throw new Exception("Error while saving barcode to disk.", 1);
        }

        $output = 'data:' . mime_content_type($filename) . ';base64,' . base64_encode(file_get_contents($filename));;
        unlink($filename);

        return $output;
    }

    /**
     * Call functions to generate barcode
     * 
     * @return null
     */
    private function _make()
    {
        // Convert text to barcode data
        $this->_outputText = $this->_type->convert($this->_inputText);

        // Calculate size based on text
        $this->_fitText();
        // Create image
        $this->_create();
    }

    /**
     * Calculate barcode width/height (depends on orientation)
     * 
     * @return null
     */
    private function _fitText()
    {
        $textSize = $this->_pad;
        for ($i = 1; $i <= strlen($this->_outputText); $i++) {
            $textSize += substr($this->_outputText, ($i - 1), 1);
        }

        if ($this->_orientation == self::BARCODE_ORIENTATION_HORIZONTAL) {
            if ($this->_dimensions['width'] > $textSize) {
                $this->_dimensions['ratio'] = $this->_dimensions['width'] / $textSize;
                $this->_dimensions['width'] = $this->_dimensions['width'];
            } else {
                $this->_dimensions['ratio'] = 1;
                $this->_dimensions['width'] = $textSize;
            }
        } else {
            if ($this->_dimensions['height'] > $textSize) {
                $this->_dimensions['ratio'] = $this->_dimensions['height'] / $textSize;
                $this->_dimensions['height'] = $this->_dimensions['height'];
            } else {
                $this->_dimensions['ratio'] = 1;
                $this->_dimensions['height'] = $textSize;
            }
        }
    }

    /**
     * Create image using GD Image Library
     * 
     * @return null
     */
    private function _create()
    {
        if ($this->_barcode !== null) {
            imagedestroy($this->_barcode);
        }

        $this->_barcode = imagecreate($this->_dimensions['width'], $this->_dimensions['height']);

        $white = imagecolorallocate($this->_barcode, 255, 255, 255);
        $black = imagecolorallocate($this->_barcode, 0, 0, 0);

        imagefill($this->_barcode, 0, 0, $white);

        $location = $this->_pad / 2;
        for ($i = 1; $i <= strlen($this->_outputText); $i++) {
            $barSize = $location + (substr($this->_outputText, ($i - 1), 1));

            if ($this->_orientation == self::BARCODE_ORIENTATION_HORIZONTAL) {
                imagefilledrectangle($this->_barcode, $location * $this->_dimensions['ratio'], 0, $barSize * $this->_dimensions['ratio'], $this->_dimensions['height'], ($i % 2 == 0 ? $white : $black));
            } else {
                imagefilledrectangle($this->_barcode, 0, $location * $this->_dimensions['ratio'], $this->_dimensions['width'], $barSize * $this->_dimensions['ratio'], ($i % 2 == 0 ? $white : $black));
            }

            $location = $barSize;
        }
    }
}
