<p align="center" style="background: white; padding: 40px 20px 20px 20px"><a href="https://laramate.de" target="_blank"><img src="https://laramate.de/laramate.webp" width="200" alt="Laravel Logo"></a></p>

# Auto Create UUID Trait

The `AutoCreateUuid` trait automatically populates a UUID column on Eloquent models
when they are created. If the UUID attribute is empty or invalid, a new UUID (v4)
is generated before the model is persisted.

## Usage

Add the trait to your Eloquent model:

```php
use Illuminate\Database\Eloquent\Model;
use Laramate\Support\Traits\AutoCreateUuid;

class Document extends Model
{
    use AutoCreateUuid;
}
```

Make sure your table has a `uuid` column:

```php
Schema::create('documents', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique();
    // ...
});
```

That's it. On `creating`, the trait checks the UUID column and fills it
automatically:

```php
$document = Document::create(['title' => 'Invoice']);

$document->uuid; // "9c2f6b1e-3d4a-4f8b-9c1d-2e5a7b8c9d0e"
```

A UUID that is already set **and valid** will not be overwritten:

```php
$document = Document::create([
    'title' => 'Invoice',
    'uuid'  => '550e8400-e29b-41d4-a716-446655440000',
]);

$document->uuid; // "550e8400-e29b-41d4-a716-446655440000"
```

Invalid values are replaced with a freshly generated UUID.

## Custom column name

By default the trait uses the `uuid` column. To use a different column, define
the `$uuid_column` property on your model:

```php
class Document extends Model
{
    use AutoCreateUuid;

    protected $uuid_column = 'external_id';
}
```

## Renewing the UUID

You can manually generate a new UUID at any time using `renewUuid()`. The method
returns the model instance, so it can be chained:

```php
$document->renewUuid()->save();
```

## API

| Method | Description |
|---|---|
| `getUuidColumn(): string` | Returns the UUID column name (default: `uuid`) |
| `renewUuid(): static` | Sets a freshly generated UUID (v4) on the model |


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
