<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Suppport\Facades\Gate;

class AuthServiceProvider extends ServiceProvider{
     /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */

     protected $policies = [
          // 'App\Models\Model' => 'App\Policies\ModelPolicy'
     ];

      /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}