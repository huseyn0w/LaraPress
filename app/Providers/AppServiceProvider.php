<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Phase 4: the public theme is Tailwind. Use Laravel's Tailwind
        // paginator so ->links() (and the pretty_url()/pretty_search_url()
        // helpers that wrap it) emit Tailwind markup instead of Bootstrap.
        Paginator::useTailwind();

        view()->composer('*', function ($view)
        {
            $view->with('current_user', \Auth::user());
            $view->with('home_page_data', get_data(1, 'page', ['slug', 'title']));
        });
    }
}
