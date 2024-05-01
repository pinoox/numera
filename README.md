**Numera: A PHP Library for Number to Words Conversion**
=====================================================

[![Latest Stable Version](https://poser.pugx.org/pinoox/numera/v/stable)](https://packagist.org/packages/pinoox/numera)
[![GitHub Stars](https://img.shields.io/github/stars/pinoox/numera.svg)](https://github.com/pinoox/numera/stargazers)
[![GitHub Forks](https://img.shields.io/github/forks/pinoox/numera.svg)](https://github.com/pinoox/numera/network)
[![GitHub Issues](https://img.shields.io/github/issues/pinoox/numera.svg)](https://github.com/pinoox/numera/issues)
[![License](https://img.shields.io/github/license/pinoox/numera.svg)](https://github.com/pinoox/numera/blob/master/LICENSE)
[![Total Downloads](https://poser.pugx.org/pinoox/numera/downloads)](https://packagist.org/packages/pinoox/numera)

Numera is a PHP library that provides a simple and efficient way to convert numbers to words and vice versa. It supports multiple languages and can be easily extended to support more languages.

* [Features](#features)
* [Installation and Setup](#installation-and-setup)
    * [Install via Composer](#install-via-composer)
    * [Initialize Numera](#initialize-numera)
* [Usage](#usage)
    * [Convert Numbers to Words](#convert-numbers-to-words)
    * [Convert Numbers to Summary Words](#convert-numbers-to-summary)
    * [Convert Words to Numbers](#convert-words-to-numbers)
    * [Use Camel Case](#use-camel-case)
* [Supported Languages](#supported-languages)
* [Set Locale](#set-locale)
* [Set Locale Fallback](#set-locale-fallback)
* [Get Translates](#get-translates)
* [Add Translate](#add-translate)
* [Create a New Language](#create-a-new-language)
* [Author](#author)
* [Contributing](#contributing)
* [Documentation](#documentation)
* [License](#license)

**Features**
------------

* Convert numbers to words (e.g. 1234 to "one thousand two hundred thirty-four")
* Convert words to numbers (e.g. "one thousand two hundred thirty-four" to 1234)
* Support for multiple languages (currently English and Persian, with more to come)
* Camel case support for output words
* Easy to use and extend


**Installation and Setup**
-------------------------

### Install via Composer

You can install Numera using Composer:
```
composer require pinoox/numera
```

### Initialize Numera

To use Numera, you need to initialize it with a locale. You can do this using the `init` method:
```php
use Pino\Numera;

$numera = Numera::init('en'); // Initialize with English locale
```

### Convert Numbers to Words

To convert a number to words, use the `convertToWords` method:
```php
$result = $numera->convertToWords(4454545156);
echo $result; // Output: "four billion, four hundred fifty-four million, five hundred forty-five thousand, one hundred fifty-six"
```

Alternatively, you can use the `n2w` method for a simpler syntax:
```php
$result = $numera->n2w('4,454,545,156');
echo $result; // Output: "four billion, four hundred fifty-four million, five hundred forty-five thousand, one hundred fifty-six"
```

### Convert Numbers to Summary

To convert a number to summary words, use the `convertToSummary` method:
```php
$result = $numera->convertToSummary(4454545156);
echo $result; // Output: "4 Billion, 454 Million, 545 Thousand, 156"
```

Alternatively, you can use the `n2w` method for a simpler syntax:
```php
$result = $numera->n2s('4,454,545,156');
echo $result; // Output: "4 Billion, 454 Million, 545 Thousand, 156"
```
### Convert Words to Numbers

To convert words to a number, use the `convertToNumber` method:
```php
$result = $numera->convertToNumber('four billion, four hundred fifty-four million, five hundred forty-five thousand, one hundred fifty-six');
echo $result; // Output: 4454545156
```

Alternatively, you can use the `w2n` method for a simpler syntax:
```php
$result = $numera->w2n("4 Billion, 454 Million, 545 Thousand, 156");
echo $result; // Output: 4454545156
```

You can also specify separators for the `w2n` method:
```php
$result = $numera->w2n('four billion, four hundred fifty-four million, five hundred forty-five thousand, one hundred fifty-six', [' ', ',']);
echo $result; // Output: 4454545156
```

### Use Camel Case

To use camel case for output words, use the `setCamelCase` method:
```php
$numera->setCamelCase(true);
$result = $numera->convertToWords('4,454,545,156');
echo $result; // Output: "Four Billion, Four Hundred Fifty-Four Million, Five Hundred Forty-Five Thousand, One Hundred Fifty-Six"
```

### Supported Languages

Numera currently supports the following languages:

* English (en)
* Persian (fa)

### Set Locale

To set the locale for the Numera object, use the `setLocale` method:
```php
$numera->setLocale('fa'); // Set locale to Persian
```

### Set Locale Fallback

To set the fallback locale for the Numera object, use the `setLocaleFallback` method:
```php
$numera->setLocaleFallback('en'); // Set fallback locale to English
```

### Get Translates

To get the translates for the current locale, use the `getTranslates` method:
```php
$translates = $numera->getTranslates();
print_r($translates); // Output: Array of translates for the current locale
```

### Add Translate

To add translates for a specific locale, use the `addTranslate` method:
```php
$numera->addTranslate('fr', ['four' => 'quatre']); // Add French translates
```

### Add Translate File

To add translates by array file for a specific locale, use the `addTranslateFile` method:
```php
$numera->addTranslateFile('fr','/path/lang/fr.php'); // Add French translates
```

### Create a New Language

If you want to add support for a new language, please read our [Translation Guide](TranslationGuide.md) for a step-by-step guide on how to create a new language pack.

**Author**
---------

Numera was created by [Pinoox](https://www.pinoox.com/).

**Contributing**
------------

If you'd like to contribute to Numera, please fork the repository and submit a pull request. We'd love to have your help.

**Documentation**
-------------

* [README.md](README.md) - Documents
* [TranslationGuide.md](TranslationGuide.md) - Guide for creating a new language pack

**License**
---------

Numera is licensed under the [MIT License](https://opensource.org/licenses/MIT). See the LICENSE file for more information. 
