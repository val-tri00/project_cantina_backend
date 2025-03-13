<?php 

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider{
    /**
     * Definirea directorului implicit pentru controller-e.
     */

    protected $namespace = 'App\Http\Controllers';

    /**
         * Înregistrează rutele aplicației.
     */

    public function boot(){

        $this->routes(function () {
            Route::prefix('api') 
                ->middleware('api') 
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php')); 
        });


        Route::get('/login', function() {
            return response()->json(['error' => 'Unauthorized'], 401);
        })-> name('login');
    }
}