<?php

namespace App\Providers;

use App\Models\Kriteria;
use App\Models\Kriteriakomparison;
use App\Observers\KriteriaKomparisonObserver;
use App\Observers\KriteriaObserver;
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
        Kriteriakomparison::observe(KriteriaKomparisonObserver::class);
        Kriteria::observe(KriteriaObserver::class);
    }
}
