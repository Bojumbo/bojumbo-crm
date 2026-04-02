<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use App\Listeners\SendLoginNotification;

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
        Event::listen(Login::class, SendLoginNotification::class);

        if (env('FORCE_HTTPS')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        if (!app()->runningInConsole() && Schema::hasTable('settings')) {
            View::share('currency', Setting::get('crm_currency', '₪'));
        } else {
            View::share('currency', '₪');
        }
    }
}