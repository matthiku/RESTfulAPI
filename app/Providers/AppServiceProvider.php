<?php

namespace App\Providers;

use App\User;
use App\Product;
use App\Mail\UserCreated;
use App\Mail\UserMailChanged;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{



    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Each time a new user is created, we send the verification email
        User::created( function ($user) {
            // https://laravel.com/docs/5.5/helpers#method-retry
            retry(5, function() use($user) {
                Mail::to($user)->send(new UserCreated($user));
            }, 100);
        });

        // Each time a user changes their email address, we re-send the verification email
        User::updated( function ($user) {
            if ($user->isDirty('email')) {
                retry(5, function() use($user) {
                    Mail::to($user)->send(new UserMailChanged($user));
                }, 100);
            }
        });

        // Each time the product is updated, check the remaining quantity
        Product::updated(function ($product) {
            if ($product->quantity == 0 && $product->isAvailable()) {
                $product->status = Product::UNAVAILABLE_PRODUCT;

                $product->save();
            }
        });
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
