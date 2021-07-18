<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Controllers
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MessageController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['auth:sanctum']], function(){
    Route::get('/users/me', [UserController::class, 'me'])->name('users.me'); // Pegar o usuário logado
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');

    Route::get('/messages/{user}', [MessageController::class, 'listMessages'])->name('messages.listMessages');
    Route::post('/messages/store', [MessageController::class, 'store'])->name('messages.store');

});
