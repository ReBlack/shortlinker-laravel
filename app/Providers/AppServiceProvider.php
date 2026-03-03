<?php

namespace App\Providers;

use App\Bridges\QrCodeForUrl;
use App\Models\Url;
use App\Services\Bridges\IQrCodeForUrl;
use App\Services\Models\IUrlModel;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(IQrCodeForUrl::class, QrCodeForUrl::class);
        $this->app->bind(IUrlModel::class, Url::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
