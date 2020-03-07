<?php

namespace App\Providers;

use App\Constants\PatternsConstants;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * register any application services.
     * @return void
     */
    public function register()
    {
    }

    /**
     * bootstrap any application services.
     * @return void
     */
    public function boot()
    {
        Validator::extend('filter', function ($attribute, $value, $parameters, $validator) {
            if (!empty(preg_match(PatternsConstants::FILTER, $value))) {
                return true;
            }
            return false;
        });

        Validator::extend('uuid', function ($attribute, $value, $parameters, $validator) {
            if (!empty(preg_match(PatternsConstants::UUID, $value))) {
                return true;
            }
            return false;
        });
    }
}
