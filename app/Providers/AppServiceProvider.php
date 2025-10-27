<?php

namespace App\Providers;

use App\Contracts\ClinicServiceInterface;
use App\Contracts\DistanceServiceInterface;
use App\Services\ClinicService;
use App\Services\DistanceService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ClinicServiceInterface::class, ClinicService::class);
        $this->app->bind(DistanceServiceInterface::class, DistanceService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // JsonResource::withoutWrapping();
        Model::preventLazyLoading(! $this->app->isProduction());
    }
}
