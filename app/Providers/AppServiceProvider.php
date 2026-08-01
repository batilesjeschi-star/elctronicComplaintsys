<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
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
        // Make {{ $items->links() }} render Bootstrap 5 markup everywhere,
        // instead of Laravel's default Tailwind pagination styles.
        Paginator::useBootstrapFive();
    }
}
