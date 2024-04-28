**Numera: A PHP Library for Number to Words Conversion**
=====================================================

Numera is a PHP library that provides a simple and efficient way to convert numbers to words and vice versa. It supports multiple languages and can be easily extended to support more languages.

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
### Manual Installation

You can also download the library manually and include it in your project:
```php
require_once 'path/to/Numera/autoload.php';
```
### Initialize Numera

To use Numera, you need to initialize it with a locale. You can do this using the `init` method:
```php
use Translate\Numera;

$number = Numera::init('en'); // Initialize with English locale
```

### Convert Numbers to Words

To convert a number to words, use the `convertNumeraToWords` method:
```php
$result = $number->convertNumeraToWords(4454545156);
echo $result; // Output: "four billion, four hundred fifty-four million, five hundred forty-five thousand, one hundred fifty-six"
```

Alternatively, you can use the `n2w` method for a simpler syntax:
```php
$result = $number->n2w('4,454,545,156');
echo $result; // Output: "four billion, four hundred fifty-four million, five hundred forty-five thousand, one hundred fifty-six"
```

### Convert Words to Numbers

To convert words to a number, use the `convertWordsToNumera` method:
```php
$result = $number->convertWordsToNumera('four billion, four hundred fifty-four million, five hundred forty-five thousand, one hundred fifty-six');
echo $result; // Output: 4454545156
```

Alternatively, you can use the `w2n` method for a simpler syntax:
```php
$result = $number->w2n('four billion, four hundred fifty-four million, five hundred forty-five thousand, one hundred fifty-six');
echo $result; // Output: 4454545156
```

You can also specify separators for the `w2n` method:
```php
$result = $number->w2n('four billion, four hundred fifty-four million, five hundred forty-five thousand, one hundred fifty-six', [' ', ',']);
echo $result; // Output: 4454545156
```

### Use Camel Case

To use camel case for output words, use the `setCamelCase` method:
```php
$number->setCamelCase(true);
$result = $number->convertNumeraToWords('4,454,545,156');
echo $result; // Output: "Four Billion, Four Hundred Fifty-Four Million, Five Hundred Forty-Five Thousand, One Hundred Fifty-Six"
```

### Example

Here is an example of using Numera to convert a number to words with camel case:
```php
$number = Numera::init('en');
$result = $number->setCamelCase(true)->convertNumberToWords(4454545156);
$this->assertEquals('Four Billion, Four Hundred Fifty-Four Million, Five Hundred Forty-Five Thousand, One Hundred Fifty-Six', $result);
```

### Supported Languages

Numera currently supports the following languages:

* English (en)
* Persian (fa)

### Set Locale

To set the locale for the Numera object, use the `setLocale` method:
```php
$number->setLocale('fa'); // Set locale to Persian
```

### Set Locale Fallback

To set the fallback locale for the Numera object, use the `setLocaleFallback` method:
```php
$number->setLocaleFallback('en'); // Set fallback locale to English
```

### Get Translates

To get the translates for the current locale, use the `getTranslates` method:
```php
$translates = $number->getTranslates();
print_r($translates); // Output: Array of translates for the current locale
```

### Add Translate

To add translates for a specific locale, use the `addTranslate` method:
```php
$number->addTranslate('fr', ['hello' => 'bonjour']); // Add French translates
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

* [README.md](Readme.md) - This file
* [TranslationGuide.md](TranslationGuide.md) - Guide for creating a new language pack

**License**
---------

Numera is licensed under the [MIT License](https://opensource.org/licenses/MIT). See the LICENSE file for more information. 
