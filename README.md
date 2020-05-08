# PHP Barcode Generator
Barcode generation made easy

### Introduction
Welcome to PHPBarcode generator. This is a PHP class which helps in barcode generation.
It offers some barcode formats and options to suit your needs.
Read through this documentation file to find out how to install and use the class.

### Author note
This project is based on [php-barcode](http://github.org/davidscotttufts/php-barcode) project by David Tufts. The original implementation has been made by him.
My implementation it's still a work in progress and may present errors.
Contributions, issues and questions are always welcome.

---

# Getting Started
### Installation
You may install the PHPBarcode Generator with <a href="https://getcomposer.org/" target="_blank">Composer</a>.
```
$ composer require dvdmarchetti/phpbarcode
```

### Basic Usage
First you have to include the composer-generated autoload file.
```php
<?php require 'vendor/autoload.php'; ?>
```

Then you can start generating barcodes. You just have to create a new **PHPBarcode\Barcode** object.
**PHPBarcode\Barcode**'s constructor requires at least two parameters:
  - *(IBarcodeType)* Barcode type (an object which implements **PHPBarcode\Type\IBarcodeType** interface)
  - *(string)* Barcode text
  - *(optional) (array)* Array of dimensions (with **width** and **height** index)
  - *(optional) (int)* Barcode orientation (use class constants)

```php
$barcode = new PHPBarcode\Barcode($type, $text);
$barcode->output();
```

This will output the image directly in your browser (displayed inline).

### Output options
There are three ways of displaying barcodes:

**1. Inline Display**

Display image directly in browser using built-in image viewer. (NOTE: This method changes **Content-Type** header to **image/png**).

Example:

```php
<?php $barcode->output; ?>
```

**2. As Data Url**
Generate a Base64Data image which can be printed into **<img>** tags.

Example:

```php
<img src="<?php echo $barcode->outputAsDataUrl(); ?>">
```

**3. Save File**
Save file as PNG image on server. Path and filename can be specified as parameter.

Example:

```php
<?php $barcode->save('bar.png'); ?>
<img src="bar.png">
```

```php
<?php
// Without parameter, an unique name will be generated
$filename = $barcode->save();
?>
<img src="<?php echo $filename; ?>">
```

# Documentation
PHPBarcode has a lot of options and customization, but the documentation it's still a work in progress.
