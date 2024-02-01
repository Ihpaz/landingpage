<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Vinkla\Hashids\Facades\Hashids;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $route = ['role', 'permission', 'user', 'scheduler', 'activity_log'];
        foreach ($route as $url) {
            Route::bind($url, function ($value, $route) {
                return $this->hashing($value);
            });
        }

        $this->routes(function () {
            $this->mapApiRoutes();
            $this->mapWebRoutes();
            $this->mapPassportRoutes();
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }

    private function hashing($routeKey)
    {
        return Hashids::decode($routeKey)[0] ?? App::abort(404);
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */

    protected function mapWebRoutes()
    {
        $filename = ['web', 'auth', 'cms', 'master', 'media', 'mfa', 'module'];
        foreach ($filename as $path) {
            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web/' . $path . '.php'));
        }
    }

    protected function mapApiRoutes()
    {
        $filename = ['auth', 'core', 'master'];
        foreach ($filename as $path) {
            Route::middleware(['json'])
                ->prefix('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api/' . $path . '.php'));
        }
    }

    protected function mapPassportRoutes()
    {
        Route::prefix('passport')
            ->as('passport.')
            ->namespace($this->namespace . '\Passport')
            ->group(base_path('routes/passport.php'));
    }
}
