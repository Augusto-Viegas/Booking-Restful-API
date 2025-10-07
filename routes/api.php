<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\ServiceController;
use App\Http\Controllers\Api\V1\ServiceSlotController;
use App\Http\Controllers\Api\V1\BookingController;
use App\Http\Middleware\Authenticate;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');


/**
 * Routes for employees, managers and admins
 */
Route::prefix('auth')
    ->name('api.auth.')
    ->group(function(){
        Route::post('login', [AuthController::class, 'login'])->name('login');
        Route::post('refresh', [AuthController::class, 'refresh'])->name('refresh');
        
        Route::middleware('auth:api')->group(function(){
            Route::post('me', [AuthController::class, 'me'])->name('me');
            Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        });
    });

Route::prefix('v1')
    ->name('api.v1.')
    ->group(function(){
        /**
         * Standard REST routes
         */
        Route::middleware('auth:api')->group(function(){
            Route::apiResource('customers', CustomerController::class);
            Route::apiResource('services', ServiceController::class);
            Route::apiResource('service-slot', ServiceSlotController::class);
            Route::apiResource('bookings', BookingController::class);
        });

        /**
         * Extra routes
         */
        Route::middleware('auth:api')->group(function(){
            Route::post('service-slot/extra/multiple-store', [ServiceSlotController::class, 'multipleStore']);
            Route::get('service-slot/extra/availability', [ServiceSlotController::class, 'available']);
        });
        //route::apiResource('bookings', BookingController::class);
        //route::apiResource('payments', PaymentController::class);
        //route::apiResource('admins', AdminController::class);
});

/**
 * Routes for customers of the api
 */
Route::prefix('customer')
    ->name('api.customer.')
    ->group(function(){
        /**
         * Nota: Como fazer isso? Deixar que os clientes acessem a rota sem auth
         * JWT para as funções que o cliente vai usufruir? Por exemplo,
         * para criar conta, fazer login, ver serviços disponíveis, etc.
         * E proteger com JWT as rotas que o cliente só pode acessar
         * se estiver autenticado, como ver e atualizar o próprio perfil,
         * fazer reservas, ver o histórico de reservas, etc.
         * ou deixar tudo sem proteção JWT e usar outra forma de proteção? ou não usar
         * proteção nenhuma?
        */

        /**
         * Customer registration
        */
        Route::apiResource('registration', CustomerController::class)->only(['store']);


        /**
         * Route for jwt authentication
        */
        Route::post('login', [AuthController::class, 'customerLogin'])->name('login');
        Route::post('refresh', [AuthController::class, 'customerRefresh'])->name('refresh');
        
        /**
         * Protected routes
        */
        Route::middleware('auth:customer')->group(function(){
            Route::post('me', [AuthController::class, 'customerMe'])->name('me');
            Route::post('logout', [AuthController::class, 'customerLogout'])->name('logout');
        });
    });

