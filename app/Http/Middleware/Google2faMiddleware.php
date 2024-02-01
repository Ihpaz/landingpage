<?php

namespace App\Http\Middleware;

use App\Utils\Google2FA\Authenticator;
use Closure;
use Illuminate\Http\Request;

class Google2faMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $authenticator = app(Authenticator::class)->boot($request);

        if ($authenticator->isAuthenticated()) {
            return $next($request);
        }
        
        return $authenticator->makeRequestOneTimePasswordResponse();
    }
}
