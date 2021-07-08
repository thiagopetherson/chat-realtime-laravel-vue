<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

use App\Http\Controllers\Web\PageController;


Route::get('/', [PageController::class, 'welcome'])->name('welcome');

Route::group(['middleware' => ['auth:sanctum', 'verified']], function() {
    Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');
    Route::get('/chat', [PageController::class, 'chat'])->name('chat');
});
