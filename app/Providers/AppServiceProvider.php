<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

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

        if($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Validator::resolver(function($translator, $data, $rules, $messages)
        {
            return new \App\Validators\IframeValidator($translator, $data, $rules, $messages);
        });

        Inertia::share([
            'app' => [
                'adobeReaderKey' => env('ADOBE_READER_EMBEDED_API_CLIENT_ID', null)
            ],
        ]);
    }
}
