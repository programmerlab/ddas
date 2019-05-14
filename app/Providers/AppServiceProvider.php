<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Schema::defaultStringLength(191);
	Validator::extend('mobile_check','App\\send_otp@MobileCheck');
        Validator::extend('email_check','App\\send_otp@EmailCheck');
        Validator::extend('password_check' ,'App\\send_otp@PasswordCheck');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
