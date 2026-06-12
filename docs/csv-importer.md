<p align="center" style="background: white; padding: 40px 20px 20px 20px"><a href="https://laramate.de" target="_blank"><img src="https://laramate.de/laramate.webp" width="200" alt="Laravel Logo"></a></p>

# CSV Importer

Reads a CSV file into a PHP array. Optionally uses the first line as array keys,
supports custom separator, enclosure, and escape characters, converts `NULL`
strings to real `null` values, and pads missing columns.

## Basic Usage

```php
use Laramate\Support\File\CsvImport;

$rows = CsvImport::make(storage_path('import/users.csv'))->handle();
```

Without further options each row is returned as a numerically indexed array:

```php
[
    ['1', 'Alice', 'alice@example.com'],
    ['2', 'Bob', 'bob@example.com'],
]
```

The importer uses the [Makeable Trait](makeable-trait.md), so `CsvImport::make(...)`
is equivalent to `new CsvImport(...)`.

## First Line as Keys

Pass `true` as the second argument to use the header line as array keys:

```php
$rows = CsvImport::make(storage_path('import/users.csv'), true)->handle();
```

```php
[
    ['id' => '1', 'name' => 'Alice', 'email' => 'alice@example.com'],
    ['id' => '2', 'name' => 'Bob', 'email' => 'bob@example.com'],
]
```

If a row contains fewer columns than the header, the missing keys are added
with a `null` value, so every row always contains all header keys.

## Options

All options are constructor arguments:

| Argument | Type | Default | Description |
| --- | --- | --- | --- |
| `$uri` | `string` | – | Path or URI of the CSV file. |
| `$first_line_as_keys` | `bool` | `false` | Use the first line as array keys for all rows. |
| `$separator` | `string` | `,` | Field separator character. |
| `$enclosure` | `string` | `"` | Field enclosure character. |
| `$escape` | `string` | `\` | Escape character. |

Example with a semicolon-separated file:

```php
$rows = CsvImport::make(
    uri: storage_path('import/users.csv'),
    first_line_as_keys: true,
    separator: ';',
)->handle();
```

## NULL Handling

Field values that contain the literal string `NULL` are converted to real
`null` values. All other values are returned as strings, as read by PHP's
native CSV parsing.

---
### About Laramate
We build high-performance custom software and CRM systems that adapt to you. Leveraging
the power of Laravel, React, and Statamic, we create digital experiences tailored
specifically to your operational needs.

---
&copy; 2026 Laramate
&nbsp;&bull;&nbsp; [MIT License](../LICENSE.md)
&nbsp;&bull;&nbsp; [www.laramate.de][Laramate Website]
&nbsp;&bull;&nbsp; [github.com/Laramate][Laramate Github]

<!-- Common References -->
[logo]: https://avatars1.githubusercontent.com/u/45978330?s=100
[Laramate Website]: http://www.laramate.de
[Laramate Github]: https://github.com/Laramate
