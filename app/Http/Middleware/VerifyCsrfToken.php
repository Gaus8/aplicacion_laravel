<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Blade forms submit the session token in a hidden field; this app does not
     * use the JavaScript-readable XSRF-TOKEN cookie for AJAX requests.
     */
    public function shouldAddXsrfTokenCookie(): bool
    {
        return false;
    }
}
