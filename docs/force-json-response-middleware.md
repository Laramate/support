<p align="center" style="background: white; padding: 40px 20px 20px 20px"><a href="https://laramate.de" target="_blank"><img src="https://laramate.de/laramate.webp" width="200" alt="Laravel Logo"></a></p>

# ForceJsonResponse Middleware
The ForceJsonResponse middleware ensures that the application consistently communicates 
via JSON by forcing the Accept header to application/json for all incoming requests. 
This is a critical utility for API-driven domains where you want to prevent Laravel 
from returning HTML redirects or views during validation errors or authentication failures.

## Registration
In a modern Laravel 13 application, register this middleware in bootstrap/app.php. 
It is best applied to the api middleware group:

```php
// bootstrap/app.php

use Laramate\Support\Middleware\ForceJsonResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(append: [
            ForceJsonResponse::class,
        ]);
    })
    ->create();
```

## Technical Note
This middleware manipulates the request before it reaches the controller or validation logic. 
It does not modify the response content-type directly; instead, it signals to the rest of the 
Laravel pipeline that the client "prefers" JSON, triggering the framework's internal JSON-first logic.

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