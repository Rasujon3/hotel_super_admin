<?php

use App\Http\Controllers\PackageController;
use App\Http\Controllers\PopularPlaceController;
use App\Http\Controllers\PropertyTypeController;
use App\Http\Controllers\WithdrawController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\AccessController;
use App\Http\Controllers\DashboardController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [IndexController::class, 'loginPage'])->name('login-admin');
Route::post('store-api-token', [AccessController::class, 'storeApiToken']);

Route::get('/logout', [AccessController::class, 'logout'])->name('logout');

Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

Route::resource('packages', PackageController::class);
Route::resource('popularPlaces', PopularPlaceController::class);
Route::resource('withdraws', WithdrawController::class);

# web routes
Route::resource('propertyTypes', PropertyTypeController::class);

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');

    return 'All caches (config, route, view, application) have been cleared!';
});
