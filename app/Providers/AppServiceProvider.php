<?php

namespace App\Providers;

use App\Models\Product;
use App\Policies\Api\ProductPolicy;
use App\Services\Contracts\InvoiceServiceContract;
use App\Services\InvoiceService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    public array $bindings = [
        \App\Repositories\Contract\ProductsRepositoryContract::class => \App\Repositories\ProductsRepository::class,
        \App\Repositories\Contract\ImagesRepositoryContract::class => \App\Repositories\ImageRepository::class,
        \App\Services\Contracts\FileServiceContract::class => \App\Services\FileService::class,
        \App\Services\Contracts\PaypalServiceContract::class => \App\Services\PaypalService::class,
        \App\Repositories\Contract\OrderRepositoryContract::class => \App\Repositories\OrderRepository::class,
        InvoiceServiceContract::class => InvoiceService::class,
    ];
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->isLocal()) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        Gate::policy(Product::class, ProductPolicy::class);
    }
}
