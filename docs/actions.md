<p align="center" style="background: white; padding: 40px 20px 20px 20px"><a href="https://laramate.de" target="_blank"><img src="https://laramate.de/laramate.webp" width="200" alt="Laravel Logo"></a></p>

# Actions

An Action is a standalone class that encapsulates exactly one piece of business
logic – e.g. `CreateOrderAction`, `RegisterUserAction` or `UpdateInventoryAction`.
Instead of stuffing validation, queries and side effects into a controller
("fat controller"), the controller simply delegates to an Action.

**Why Actions?**

- **Single responsibility** – one class, one job. Easy to read, easy to change.
- **Reusability** – the same Action works in a controller, an Artisan command,
  a scheduled job or an API endpoint. No copy & paste.
- **Testability** – small, isolated units are trivial to test.
- **Queue-ready** – every Action can be dispatched to the queue without any
  extra setup.

Read more in our blog post:
[Actions – Mehr Ordnung in der Laravel Business-Logik](https://laramate.de/blog/actions-mehr-ordnung-in-der-laravel-business-logik)
([English version](https://laramate.de/en/blog/actions-bringing-structure-to-laravel-business-logic)).

## Defining an Action

Extend the abstract `Action` class and implement `handle()`. Dependencies and
input data go into the constructor:

```php
use Laramate\Support\Tasks\Action;

class CreateOrderAction extends Action
{
    public function __construct(
        protected User $user,
        protected array $items,
    ) {}

    public function handle(): Order
    {
        $order = $this->user->orders()->create([
            'total' => collect($this->items)->sum('price'),
        ]);

        $order->items()->createMany($this->items);

        return $order;
    }
}
```

## Synchronous execution

Run an Action immediately and use its return value. The `Makeable` trait
provides a fluent `make()` constructor:

```php
$order = CreateOrderAction::make($user, $items)->handle();

// or via the bus, running immediately on the sync queue:
CreateOrderAction::dispatchSync($user, $items);
```

Note: since Actions implement `ShouldQueue`, `dispatchSync()` runs through the
sync queue and does **not** return the result of `handle()`. Use
`make(...)->handle()` when you need the return value.

## Asynchronous execution

Every Action implements `ShouldQueue` and uses Laravel's native
`Dispatchable`, `Queueable`, `InteractsWithQueue` and `SerializesModels`
traits. It behaves exactly like a queued job:

```php
CreateOrderAction::dispatch($user, $items);

// with the usual queue options:
CreateOrderAction::dispatch($user, $items)
    ->onQueue('orders')
    ->delay(now()->addMinutes(5));

// conditionally:
CreateOrderAction::dispatchIf($user->isActive(), $user, $items);
CreateOrderAction::dispatchUnless($user->isBanned(), $user, $items);
```

Eloquent models passed to the constructor are serialized by reference
(`SerializesModels`) and re-fetched from the database when the queued
Action runs.

## Testing

Actions are tested like any queued job:

```php
use Illuminate\Support\Facades\Queue;

// assert dispatching
Queue::fake();
CreateOrderAction::dispatch($user, $items);
Queue::assertPushed(CreateOrderAction::class);

// test the logic itself – no queue involved
$order = CreateOrderAction::make($user, $items)->handle();
$this->assertEquals(150, $order->total);
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
