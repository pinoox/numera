**Translation Guide**
=====================
### Description
The Numera translation guide is a comprehensive resource for creating language files for the Numera component. This guide provides a list of keys and their corresponding English translations, which can be used to format and manipulate numbers in various languages. By following this guide, you can create a language file that is compatible with the Numera component, allowing you to use it in your application with ease.

### Numbers

| Key           | English Translation (Camel Case) | Required |
|---------------|----------------------------------|----------|
| zero          | Zero                             | *        |
| one           | One                              | *        |
| two           | Two                              | *        |
| three         | Three                            | *        |
| four          | Four                             | *        |
| five          | Five                             | *        |
| six           | Six                              | *        |
| seven         | Seven                            | *        |
| eight         | Eight                            | *        |
| nine          | Nine                             | *        |
| ten           | Ten                              | *        |
| eleven        | Eleven                           | *        |
| twelve        | Twelve                           | *        |
| thirteen      | Thirteen                         | *        |
| fourteen      | Fourteen                         | *        |
| fifteen       | Fifteen                          | *        |
| sixteen       | Sixteen                          | *        |
| seventeen     | Seventeen                        | *        |
| eighteen      | Eighteen                         | *        |
| nineteen      | Nineteen                         | *        |
| twenty        | Twenty                           | *        |
| thirty        | Thirty                           | *        |
| forty         | Forty                            | *        |
| fifty         | Fifty                            | *        |
| sixty         | Sixty                            | *        |
| seventy       | Seventy                          | *        |
| eighty        | Eighty                           | *        |
| ninety        | Ninety                           | *        |
| one_hundred   | Own Hundred                      | -        |
| two_hundred   | Two Hundred                      | -        |
| three_hundred | Three Hundred                    | -        |
| four_hundred  | Four Hundred                     | -        |
| five_hundred  | Five Hundred                     | -        |
| sex_hundred   | Sex Hundred                      | -        |
| seven_hundred | Seven Hundred                    | -        |
| eight_hundred | Eight Hundred                      | -        |
| nine_hundred  | Nine Hundred                    | -        |

### Units

| Key | English Translation (Camel Case) | Required |
| --- | --- | --- |
| hundred | Hundred | * |
| thousand | Thousand |* |
| million | Million | * |
| billion | Billion |* |
| trillion | Trillion |* |
| quadrillion | Quadrillion |* |
| quintillion | Quintillion |* |

### Separators

| Key | English Translation (Camel Case) | Required |
| --- |----------------------------------|----------|
| between | '&emsp;'                         | -        |
| between.part | ' , '                            | -        |
| between.thousand | '&emsp;'                         | -        |
| between.ten | ' - '                            | -        |
| between.hundred | '&emsp;'                         | -        |
| between.hundred.prefix | '&emsp;'                         | -        |

**Sample PHP Code**
-------------------

Here is an example of how the translation guide can be used in PHP:
```php
<?php
return [
    'zero' => 'Zero',
    'one' => 'One',
    'two' => 'Two',
    // ...
    'hundred' => 'Hundred',
    'thousand' => 'Thousand',
    'million' => 'Million',
    // ...
    'between' => 'Between',
    'between.part' => 'BetweenPart',
];
```

