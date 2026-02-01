<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Policies\UserPolicy;
use App\Policies\OrderPolicy;
use App\Policies\ProductPolicy;
use App\Policies\CategoryPolicy;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Gate;
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
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(User::class, UserPolicy::class);

        $this->defineGates();
    }

    protected function defineGates(): void
    {

        Gate::define('is_admin', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('is_customer', function ($user) {
            return $user->role === 'customer';
        });

        Gate::define('manage_products', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('manage_categories', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('manage_orders', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('manage_users', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('access_dashboard', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('update_order_status', function ($user, $order) {
            return $user->isAdmin() || $user->id === $order->user->id;
        });

        Gate::define('cancel_order', function ($user, $order) {
            return $user->id === $order->user->id && $order->status === 'pending';
        });


    }
}
