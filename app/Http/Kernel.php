<?php
class Kernel extends HttpKernel
{
    // ...

    protected $middlewareAliases = [
        // ...

        'with_fast_api_key' => \App\Http\Middleware\VerifyFastApiKey::class,
    ];

}
 ?>