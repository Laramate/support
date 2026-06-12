<p align="center" style="background: white; padding: 40px 20px 20px 20px"><a href="https://laramate.de" target="_blank"><img src="https://laramate.de/laramate.webp" width="200" alt="Laravel Logo"></a></p>

# Laramate Support

A collection of helpers, traits, and extensions for Laravel that we use across our
agency projects. It bundles common building blocks, from queueable actions to
model versioning, into a single, lightweight package.

Supports **Laravel 12** and **Laravel 13**.

> **Warning:** This package is under constant development. Use at your own risk.

## Installation

Install the package via Composer:

```bash
composer require laramate/support
```

## Features

| Feature | Description |
| --- | --- |
| [Actions](docs/actions.md) | Queueable, self-dispatching action classes for encapsulating business logic. Extend the base `Action`, implement `handle()`, and dispatch it synchronously or via the queue. |
| [Translatable Enums](docs/translatable-enums.md) | An `Enum` trait with a `TranslatableEnum` interface that resolves enum values through Laravel's translation files and converts cases into select arrays for forms. |
| [Makeable Trait](docs/makeable-trait.md) | Adds a static `make()` factory method to any class, allowing fluent instantiation without the `new` keyword. |
| [Auto Create UUID Trait](docs/auto-create-uuid-trait.md) | Automatically generates a UUID for Eloquent models on creation. The column name is configurable and invalid or missing UUIDs are renewed transparently. |
| [CSV Importer](docs/csv-importer.md) | Reads CSV files into arrays with configurable separator, enclosure, and escape characters. Optionally uses the first line as array keys and normalizes `NULL` values. |
| [Simple Versioning](docs/simple-versioning.md) | A `Versioning` trait for Eloquent models that creates immutable model versions with version IDs, labels, and author tracking. |
| [Data Mapper](docs/data-mapper.md) | An abstract mapper for converting raw input arrays into a defined attribute structure using mapping rules and default values. |
| [Numbering Formatter](docs/numbering-formatter.md) | Formatters for converting positions into numbering schemes, e.g. natural numbers with optional zero-padding. |
| [ForceJsonResponse Middleware](docs/force-json-response-middleware.md) | Forces the `Accept: application/json` header on incoming requests so APIs consistently return JSON instead of HTML redirects or views. |

---
### About Laramate
We build high-performance custom software and CRM systems that adapt to you. Leveraging
the power of Laravel, React, and Statamic, we create digital experiences tailored
specifically to your operational needs.

---
&copy; 2026 Laramate
&nbsp;&bull;&nbsp; [MIT License](LICENSE.md)
&nbsp;&bull;&nbsp; [www.laramate.de][Laramate Website]
&nbsp;&bull;&nbsp; [github.com/Laramate][Laramate Github]

<!-- Common References -->
[logo]: https://avatars1.githubusercontent.com/u/45978330?s=100
[Laramate Website]: http://www.laramate.de
[Laramate Github]: https://github.com/Laramate
