<p align="center" style="background: white; padding: 40px 20px 20px 20px"><a href="https://laramate.de" target="_blank"><img src="https://laramate.de/laramate.webp" width="200" alt="Laravel Logo"></a></p>

# Data Mapper

The `Mapper` converts raw input data (e.g. CSV rows, API payloads) into a clean
attribute array. Input may be an `array`, an Eloquent model, a `Collection`,
any `Arrayable` or `Traversable`, or a plain object like `stdClass` (converted
recursively). Define a mapper by extending the abstract class:

```php
use Laramate\Support\Mapper\Mapper;

class UserMapper extends Mapper
{
    protected array $attributes = ['name', 'email', 'city', 'age', 'country'];

    protected array $map = [
        'email' => 'mail_address',     // simple key mapping
        'city'  => 'address.city',     // dot notation for nested data
    ];

    protected array $defaults = [
        'country' => 'DE',
    ];

    protected array $casts = [
        'age' => 'int',
    ];
}

$result = UserMapper::convert($row);
$results = UserMapper::convertMany($rows);
```

## Resolution order

For each attribute the value is resolved in this order:

1. **Map method** — `mapFullName()` for the attribute `full_name`
2. **Map array** — a data key (dot notation supported) or a `Closure` that receives the input data
3. **Direct key** — the attribute name itself in the input data
4. **Default** — `defaultCountry()` method, or the `$defaults` array (values may be Closures)

Defaults are only used when the attribute is *missing*. An explicit `null`
in the input data is kept as `null`.

## Magic methods

For full control over a single attribute, define a `map{Attribute}()` or
`default{Attribute}()` method. The attribute name is converted to StudlyCase,
with dots treated like underscores:

| Attribute   | Map method      | Default method      |
|-------------|-----------------|---------------------|
| `name`      | `mapName()`     | `defaultName()`     |
| `full_name` | `mapFullName()` | `defaultFullName()` |
| `user.name` | `mapUserName()` | `defaultUserName()` |

Map methods take precedence over the `$map` array, default methods over the
`$defaults` array. A `null` returned from a map method is kept as `null` and
does **not** fall back to the default.

Inside these methods, `$this->get('key', $default)` reads from the input
data with dot notation support:

```php
protected function mapFullName(): ?string
{
    return trim($this->get('first_name').' '.$this->get('last_name')) ?: null;
}

protected function defaultCountry(): string
{
    return config('app.default_country', 'DE');
}
```

## Closures

`$map`, `$defaults` and `$casts` also accept Closures. Map and default
Closures receive the (normalized) input data, cast Closures receive the
resolved value.

Since PHP does not allow Closures in property initializers, override the
accessor methods `map()`, `defaults()` or `casts()` instead:

```php
class UserMapper extends Mapper
{
    protected array $attributes = ['full_name', 'country', 'name'];

    public function map(): array
    {
        return [
            'full_name' => fn (array $data) => trim($data['first_name'].' '.$data['last_name']),
        ];
    }

    public function defaults(): array
    {
        return [
            'country' => fn (array $data) => $data['locale'] === 'de_DE' ? 'DE' : 'EN',
        ];
    }

    public function casts(): array
    {
        return [
            'name' => fn ($value) => trim($value),
        ];
    }
}
```

To combine Closures with static entries, merge them:
`return array_merge($this->map, ['full_name' => fn (array $data) => ...]);`

## Casts

Available casts: `int`, `float`, `bool`, `string`, `array`, `date`,
`datetime`, any `BackedEnum` class name, or a `Closure`. `null` values
are never cast.

```php
protected array $casts = [
    'age'        => 'int',
    'status'     => UserStatus::class,
    'created_at' => 'datetime',
];
```




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
