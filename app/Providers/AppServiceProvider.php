<?php

namespace App\Providers;

use App\Models\UserApi;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(UserApi::class); // To change the santum api default table name personnel_access_token to user_api
        Blade::directive('active', function ($expression) { // Replace '@active{'route-name'}' from the master layout
            return "<?php echo request()->is($expression) ? 'active' : ''; ?>";
        });

        Blade::directive('menuOpen', function ($expression) { // Replace '@menuOpen{'route-name'}' from the master layout
            return "<?php echo request()->is($expression) ? 'active open' : ''; ?>";
        });
    }
}
