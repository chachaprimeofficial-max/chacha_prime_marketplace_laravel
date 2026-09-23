<?php
namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('*', function ($view): void {
            $view->with('headerCategories', Category::with('children')
                ->whereNull('parent_id')
                ->where('status', 1)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get());

            $view->with('cartCount', collect(session('cart', []))
                ->sum(fn ($quantity) => (int) $quantity));
        });
    }
}
