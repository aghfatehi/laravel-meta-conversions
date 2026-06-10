<?php

declare(strict_types=1);

namespace Aghfatehi\LaravelMetaConversions\Http\Middleware;

use Aghfatehi\LaravelMetaConversions\Facades\FacebookConversion;
use Closure;
use Illuminate\Http\Request;

class TrackFacebookPageView
{
    public function handle(Request $request, Closure $next): mixed
    {
        return $next($request);
    }

    public function terminate(Request $request, mixed $response): void
    {
        if ($request->method() === 'GET' && !$request->ajax() && !$request->expectsJson()) {
            try {
                FacebookConversion::pageView();
            } catch (\Throwable) {
            }
        }
    }
}
